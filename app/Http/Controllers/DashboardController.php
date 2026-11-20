<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statistik = [
            'total_produk' => Produk::count(),
            'tersedia' => Produk::where('stok', '>', 0)->count(),
            'dipesan' => Produk::where('status_produk', Produk::STATUS_DIPESAN)->count(),
            'terjual' => Produk::where('status_produk', Produk::STATUS_TERJUAL)->count(),
            'pendapatan' => (float) Pembayaran::where('status_bayar', 'berhasil')->sum('jumlah'),
            'transaksi' => Pesanan::whereIn('status_pesanan', ['dibayar', 'selesai'])->count(),
            'nilai_inventaris' => (float) Produk::where('stok', '>', 0)
                ->get(['stok', 'harga_jual'])
                ->sum(fn (Produk $produk) => $produk->stok * (float) $produk->harga_jual),
        ];

        $perGrade = Produk::query()
            ->selectRaw('grade, count(*) as jumlah')
            ->groupBy('grade')
            ->pluck('jumlah', 'grade');

        $pesananTerbaru = Pesanan::with(['pengguna', 'detail.produk'])
            ->latest('tanggal_pesanan')
            ->limit(8)
            ->get();

        $aging = Produk::with('bal')
            ->where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereDate('tanggal_rilis', '<=', now()->subDays(30)->toDateString())
                    ->orWhere(function ($inner) {
                        $inner->whereNull('tanggal_rilis')
                            ->whereDate('created_at', '<=', now()->subDays(30)->toDateString());
                    });
            })
            ->count();

        $hariIni = now()->startOfDay();
        $trenMulai = $hariIni->copy()->subDays(6);

        $pembayaranMingguIni = Pembayaran::query()
            ->where('status_bayar', 'berhasil')
            ->whereBetween('waktu_bayar', [$trenMulai, $hariIni->copy()->endOfDay()])
            ->get(['waktu_bayar', 'jumlah']);

        $namaHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        $trenPendapatan = collect(range(0, 6))->map(function (int $offset) use ($trenMulai, $pembayaranMingguIni, $namaHari): array {
            $tanggal = $trenMulai->copy()->addDays($offset);
            $total = (float) $pembayaranMingguIni
                ->filter(fn (Pembayaran $pembayaran) => $pembayaran->waktu_bayar?->isSameDay($tanggal))
                ->sum(fn (Pembayaran $pembayaran) => (float) $pembayaran->jumlah);

            return [
                'tanggal' => $tanggal->toDateString(),
                'tanggal_label' => $tanggal->format('d/m'),
                'label' => $namaHari[$tanggal->dayOfWeek],
                'total' => $total,
            ];
        });

        $pendapatanMingguIni = (float) $trenPendapatan->sum('total');

        $pendapatanMingguLalu = (float) Pembayaran::query()
            ->where('status_bayar', 'berhasil')
            ->whereBetween('waktu_bayar', [
                $trenMulai->copy()->subDays(7),
                $trenMulai->copy()->subSecond(),
            ])
            ->sum('jumlah');

        $pertumbuhan = $pendapatanMingguLalu > 0
            ? (($pendapatanMingguIni - $pendapatanMingguLalu) / $pendapatanMingguLalu) * 100
            : null;

        $kanalPenjualan = Pesanan::query()
            ->whereIn('status_pesanan', ['dibayar', 'selesai'])
            ->selectRaw('kanal, count(*) as transaksi, coalesce(sum(total_harga), 0) as omzet')
            ->groupBy('kanal')
            ->orderByDesc('omzet')
            ->get()
            ->map(fn (Pesanan $pesanan) => [
                'kanal' => $pesanan->kanal,
                'transaksi' => (int) $pesanan->transaksi,
                'omzet' => (float) $pesanan->omzet,
            ]);

        $nilaiKategori = Produk::query()
            ->join('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
            ->where('produk.stok', '>', 0)
            ->selectRaw('kategori.nama_kategori, sum(produk.stok) as unit, sum(produk.stok * produk.harga_jual) as nilai')
            ->groupBy('kategori.nama_kategori')
            ->orderByDesc('nilai')
            ->get()
            ->map(fn (Produk $produk) => [
                'nama' => $produk->nama_kategori,
                'unit' => (int) $produk->unit,
                'nilai' => (float) $produk->nilai,
            ]);

        $produkTerlaris = DetailPesanan::query()
            ->selectRaw('id_produk, sum(jumlah) as unit, sum(jumlah * harga_satuan) as omzet')
            ->whereHas('pesanan', fn ($query) => $query->whereIn('status_pesanan', ['dibayar', 'selesai']))
            ->with('produk')
            ->groupBy('id_produk')
            ->orderByDesc('unit')
            ->orderByDesc('omzet')
            ->limit(4)
            ->get();

        return view('dashboard', compact(
            'statistik',
            'perGrade',
            'pesananTerbaru',
            'aging',
            'trenPendapatan',
            'pendapatanMingguIni',
            'pertumbuhan',
            'kanalPenjualan',
            'nilaiKategori',
            'produkTerlaris',
        ));
    }
}
