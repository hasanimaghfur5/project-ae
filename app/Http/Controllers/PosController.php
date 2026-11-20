<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $transaksi = Pesanan::with(['detail.produk', 'pengguna'])
            ->where('kanal', 'pos')
            ->latest('tanggal_pesanan')
            ->limit(10)
            ->get();

        return view('pos.index', [
            'transaksi' => $transaksi,
            'sku' => $request->query('sku'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_sku' => ['required', 'string'],
            'metode' => ['required', 'in:tunai,qris'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $produk = Produk::where('kode_sku', $data['kode_sku'])->first();

        if (! $produk) {
            return back()->with('error', 'Kode SKU tidak ditemukan.');
        }

        if ($produk->stok < (int) $data['jumlah']) {
            if ($produk->stok === 0) {
                $pesan = $produk->status_produk === Produk::STATUS_DIPESAN
                    ? "Item {$produk->kode_sku} sedang dipesan pembeli online. Tunggu hingga booking kedaluwarsa."
                    : "Stok item {$produk->kode_sku} sudah habis.";

                return back()->with('error', $pesan);
            }

            return back()->with('error', "Stok item {$produk->kode_sku} tersisa {$produk->stok} unit.");
        }

        try {
            $pesanan = DB::transaction(function () use ($request, $produk, $data) {
                $jumlah = (int) $data['jumlah'];
                $item = Produk::whereKey($produk->id_produk)->lockForUpdate()->firstOrFail();

                if ($item->stok < $jumlah) {
                    throw new \RuntimeException('Stok item berubah, silakan pindai ulang.');
                }

                $total = (float) $item->harga_jual * $jumlah;

                $pesanan = Pesanan::create([
                    'id_pengguna' => $request->user()->id,
                    'kanal' => 'pos',
                    'tanggal_pesanan' => now(),
                    'batas_bayar' => null,
                    'total_harga' => $total,
                    'ongkos_kirim' => 0,
                    'status_pesanan' => 'dibayar',
                    'no_resi' => 'POS-'.strtoupper(uniqid()),
                ]);

                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item->id_produk,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $item->harga_jual,
                ]);

                $item->stok -= $jumlah;
                $item->status_produk = $item->stok > 0 ? Produk::STATUS_TERSEDIA : Produk::STATUS_TERJUAL;
                $item->save();

                Pembayaran::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'metode' => $data['metode'],
                    'jumlah' => $pesanan->total_harga,
                    'status_bayar' => 'berhasil',
                    'waktu_bayar' => now(),
                    'ref_gateway' => 'POS-'.strtoupper(uniqid()),
                ]);

                return $pesanan;
            });
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('pos.nota', $pesanan)
            ->with('status', 'Transaksi POS berhasil.');
    }

    public function nota(Pesanan $pesanan): View
    {
        abort_unless($pesanan->kanal === 'pos', 404);
        $pesanan->load(['detail.produk', 'pembayaran', 'pengguna']);

        return view('pos.nota', compact('pesanan'));
    }
}
