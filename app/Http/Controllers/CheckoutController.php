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

class CheckoutController extends Controller
{
    public const ONGKOS_KIRIM = 20000;

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $this->keranjang($request);
        if ($cart === []) {
            return redirect()->route('katalog.index')->with('error', 'Keranjang masih kosong.');
        }

        $items = Produk::with(['kategori', 'bal'])
            ->whereIn('id_produk', array_keys($cart))
            ->get()
            ->map(fn (Produk $produk): array => [
                'produk' => $produk,
                'jumlah' => $cart[$produk->id_produk],
                'subtotal' => (float) $produk->harga_jual * $cart[$produk->id_produk],
            ])
            ->values();

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $items->sum(fn (array $item) => $item['subtotal']),
            'ongkosKirim' => self::ONGKOS_KIRIM,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->keranjang($request);
        if ($cart === []) {
            return redirect()->route('katalog.index')->with('error', 'Keranjang masih kosong.');
        }

        try {
            $pesanan = DB::transaction(function () use ($cart, $request) {
                $produk = Produk::whereIn('id_produk', array_keys($cart))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_produk');

                if ($produk->count() !== count($cart)) {
                    throw new \RuntimeException('Sebagian produk tidak ditemukan.');
                }

                $subtotal = 0;

                foreach ($cart as $id => $jumlah) {
                    $item = $produk->get($id);
                    if (! $item) {
                        throw new \RuntimeException('Sebagian produk tidak ditemukan.');
                    }

                    if ($item->stok < $jumlah) {
                        throw new \RuntimeException("Stok {$item->kode_sku} tersisa {$item->stok} unit.");
                    }

                    $subtotal += (float) $item->harga_jual * $jumlah;
                }

                $pesanan = Pesanan::create([
                    'id_pengguna' => $request->user()->id,
                    'kanal' => 'website',
                    'tanggal_pesanan' => now(),
                    'batas_bayar' => now()->addMinutes(15),
                    'total_harga' => $subtotal + self::ONGKOS_KIRIM,
                    'ongkos_kirim' => self::ONGKOS_KIRIM,
                    'status_pesanan' => 'pending',
                ]);

                foreach ($cart as $id => $jumlah) {
                    $item = $produk->get($id);

                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'id_produk' => $item->id_produk,
                        'jumlah' => $jumlah,
                        'harga_satuan' => $item->harga_jual,
                    ]);

                    $item->stok -= $jumlah;
                    $item->status_produk = $item->stok > 0 ? Produk::STATUS_TERSEDIA : Produk::STATUS_DIPESAN;
                    $item->save();
                }

                Pembayaran::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'metode' => 'qris',
                    'jumlah' => $pesanan->total_harga,
                    'status_bayar' => 'pending',
                ]);

                return $pesanan;
            });
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $request->session()->forget('cart');

        return redirect()->route('pesanan.show', $pesanan)
            ->with('status', 'Pesanan dibuat. Selesaikan pembayaran dalam 15 menit.');
    }

    public function pay(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->id_pengguna === $request->user()->id, 403);

        if ($pesanan->status_pesanan !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak menunggu pembayaran.');
        }

        if ($pesanan->isExpired()) {
            $this->release($pesanan);

            return back()->with('error', 'Batas waktu pembayaran 15 menit telah habis.');
        }

        DB::transaction(function () use ($pesanan) {
            $pesanan->load('detail.produk');

            foreach ($pesanan->detail as $detail) {
                $produk = Produk::whereKey($detail->id_produk)->lockForUpdate()->first();
                if ($produk) {
                    $produk->update([
                        'status_produk' => $produk->stok > 0 ? Produk::STATUS_TERSEDIA : Produk::STATUS_TERJUAL,
                    ]);
                }
            }

            $pesanan->update([
                'status_pesanan' => 'dibayar',
                'no_resi' => 'TRF-'.strtoupper(uniqid()),
            ]);

            $pesanan->pembayaran()->where('status_bayar', 'pending')->update([
                'status_bayar' => 'berhasil',
                'waktu_bayar' => now(),
                'ref_gateway' => 'SIM-'.strtoupper(uniqid()),
            ]);
        });

        return redirect()->route('pesanan.show', $pesanan)
            ->with('status', 'Pembayaran berhasil. Pesanan sedang disiapkan.');
    }

    private function release(Pesanan $pesanan): void
    {
        DB::transaction(function () use ($pesanan) {
            $pesanan->load('detail.produk');

            foreach ($pesanan->detail as $detail) {
                $produk = Produk::whereKey($detail->id_produk)->lockForUpdate()->first();
                if ($produk) {
                    $produk->increment('stok', $detail->jumlah);
                    $produk->update(['status_produk' => Produk::STATUS_TERSEDIA]);
                }
            }

            $pesanan->update(['status_pesanan' => 'batal']);
            $pesanan->pembayaran()->where('status_bayar', 'pending')->update(['status_bayar' => 'gagal']);
        });
    }

    /**
     * @return array<int, int>
     */
    private function keranjang(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $hasil = [];

        if (array_is_list($cart)) {
            foreach ($cart as $id) {
                $hasil[(int) $id] = 1;
            }

            return $hasil;
        }

        foreach ($cart as $id => $jumlah) {
            $hasil[(int) $id] = max(1, (int) $jumlah);
        }

        return $hasil;
    }
}
