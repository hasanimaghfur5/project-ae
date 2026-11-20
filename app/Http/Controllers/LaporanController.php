<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\PemasokBal;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $dari = $request->date('dari') ?? now()->startOfMonth();
        $sampai = $request->date('sampai') ?? now();

        $pesanan = Pesanan::query()
            ->whereIn('status_pesanan', ['dibayar', 'selesai'])
            ->whereBetween('tanggal_pesanan', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()]);

        $totalPendapatan = (float) (clone $pesanan)->sum('total_harga');
        $jumlahTransaksi = (clone $pesanan)->count();
        $itemTerjual = (int) DetailPesanan::query()
            ->whereHas('pesanan', fn ($query) => $query
                ->whereIn('status_pesanan', ['dibayar', 'selesai'])
                ->whereBetween('tanggal_pesanan', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()]))
            ->sum('jumlah');

        $harian = (clone $pesanan)
            ->selectRaw('date(tanggal_pesanan) as tanggal, count(*) as transaksi, sum(total_harga) as pendapatan')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $kinerjaBal = PemasokBal::query()
            ->with('biaya')
            ->withCount([
                'produk',
                'produk as produk_terjual' => fn ($query) => $query->where('status_produk', Produk::STATUS_TERJUAL),
            ])
            ->get()
            ->map(function (PemasokBal $bal) use ($dari, $sampai) {
                $pendapatan = DetailPesanan::query()
                    ->whereHas('produk', fn ($query) => $query->where('id_bal', $bal->id_bal))
                    ->whereHas('pesanan', fn ($query) => $query
                        ->whereIn('status_pesanan', ['dibayar', 'selesai'])
                        ->whereBetween('tanggal_pesanan', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()]))
                    ->get(['harga_satuan', 'jumlah'])
                    ->sum(fn (DetailPesanan $detail) => (float) $detail->harga_satuan * $detail->jumlah);

                $biaya = (float) $bal->biaya->sum('nominal');
                $modal = (float) $bal->harga_beli;
                $laba = $pendapatan - $modal - $biaya;

                $bal->setAttribute('pendapatan', $pendapatan);
                $bal->setAttribute('biaya_operasional', $biaya);
                $bal->setAttribute('laba_bersih', $laba);
                $bal->setAttribute('roi', $modal > 0 ? ($laba / $modal) * 100 : 0);

                return $bal;
            });

        $aging = Produk::with(['bal', 'kategori'])
            ->where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereDate('tanggal_rilis', '<=', now()->subDays(30)->toDateString())
                    ->orWhere(function ($inner) {
                        $inner->whereNull('tanggal_rilis')
                            ->whereDate('created_at', '<=', now()->subDays(30)->toDateString());
                    });
            })
            ->orderBy('tanggal_rilis')
            ->get();

        $metodeBayar = Pembayaran::query()
            ->where('status_bayar', 'berhasil')
            ->whereBetween('waktu_bayar', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()])
            ->selectRaw('metode, count(*) as jumlah, sum(jumlah) as total')
            ->groupBy('metode')
            ->get();

        return view('laporan.index', compact(
            'dari', 'sampai', 'totalPendapatan', 'jumlahTransaksi', 'itemTerjual',
            'harian', 'kinerjaBal', 'aging', 'metodeBayar'
        ));
    }
}
