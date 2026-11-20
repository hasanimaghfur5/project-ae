@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-4xl text-ink">Laporan & Analitik</h1>
        <p class="mt-1 text-sm text-stone-500">Margin bersih per bal, penjualan periodik, dan deteksi stok mengendap.</p>
    </div>
    <form method="GET" class="flex flex-wrap items-end gap-2">
        <div>
            <label class="text-xs font-semibold text-stone-500">Dari</label>
            <input type="date" name="dari" value="{{ $dari->toDateString() }}" class="mt-1 block rounded-sm border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="text-xs font-semibold text-stone-500">Sampai</label>
            <input type="date" name="sampai" value="{{ $sampai->toDateString() }}" class="mt-1 block rounded-sm border border-stone-300 px-3 py-2 text-sm">
        </div>
        <button class="rounded-sm bg-stone-900 px-4 py-2 text-sm font-bold text-white hover:bg-stone-800">Terapkan</button>
    </form>
</div>

<div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-none border border-stone-200 bg-white p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Total Pendapatan</p>
        <p class="mt-2 text-4xl text-ink">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>
    <div class="rounded-none border border-stone-200 bg-white p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Transaksi</p>
        <p class="mt-2 text-4xl text-ink">{{ $jumlahTransaksi }}</p>
    </div>
    <div class="rounded-none border border-stone-200 bg-white p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-stone-400">Unit Terjual</p>
        <p class="mt-2 text-4xl text-ink">{{ $itemTerjual }}</p>
    </div>
    <div class="rounded-none border {{ $aging->count() > 0 ? 'border-amber-200 bg-amber-50' : 'border-stone-200 bg-white' }} p-5">
        <p class="text-xs font-bold uppercase tracking-wide {{ $aging->count() > 0 ? 'text-amber-600' : 'text-stone-400' }}">Stok Mengendap</p>
        <p class="mt-2 text-2xl font-extrabold {{ $aging->count() > 0 ? 'text-amber-800' : 'text-stone-900' }}">{{ $aging->count() }}</p>
    </div>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
    <div class="rounded-none border border-stone-200 bg-white p-5">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Profitabilitas per Bal</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-100 text-sm">
                <thead class="text-left text-xs font-bold uppercase tracking-wide text-stone-400">
                    <tr>
                        <th class="py-2 pr-3">Bal</th>
                        <th class="py-2 pr-3">Modal</th>
                        <th class="py-2 pr-3">Biaya</th>
                        <th class="py-2 pr-3">Pendapatan</th>
                        <th class="py-2 pr-3">Laba</th>
                        <th class="py-2 pr-3">ROI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($kinerjaBal as $item)
                        <tr>
                            <td class="py-2 pr-3">
                                <a href="{{ route('bal.show', $item) }}" class="font-bold text-stone-800 hover:text-brand-700">{{ $item->kode_bal }}</a>
                                <p class="text-xs text-stone-400">{{ $item->produk_terjual }}/{{ $item->produk_count }} terjual</p>
                            </td>
                            <td class="py-2 pr-3 text-stone-600">Rp {{ number_format((float) $item->harga_beli, 0, ',', '.') }}</td>
                            <td class="py-2 pr-3 text-stone-600">Rp {{ number_format($item->biaya_operasional, 0, ',', '.') }}</td>
                            <td class="py-2 pr-3 text-stone-600">Rp {{ number_format($item->pendapatan, 0, ',', '.') }}</td>
                            <td class="py-2 pr-3 font-bold {{ $item->laba_bersih >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">Rp {{ number_format($item->laba_bersih, 0, ',', '.') }}</td>
                            <td class="py-2 pr-3 font-semibold {{ $item->roi >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">{{ number_format($item->roi, 1, ',', '.') }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-stone-500">Belum ada bal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-none border border-stone-200 bg-white p-5">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Penjualan Harian</h2>
            <div class="mt-4 max-h-56 overflow-y-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs font-bold uppercase tracking-wide text-stone-400">
                        <tr><th class="py-1.5">Tanggal</th><th class="py-1.5">Trx</th><th class="py-1.5 text-right">Pendapatan</th></tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($harian as $row)
                            <tr>
                                <td class="py-1.5 text-stone-600">{{ \Illuminate\Support\Carbon::parse($row->tanggal)->translatedFormat('d M') }}</td>
                                <td class="py-1.5 text-stone-500">{{ $row->transaksi }}</td>
                                <td class="py-1.5 text-right font-semibold">Rp {{ number_format((float) $row->pendapatan, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-stone-500">Tidak ada penjualan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-none border border-stone-200 bg-white p-5">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Metode Pembayaran</h2>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($metodeBayar as $row)
                    <div class="flex items-center justify-between">
                        <span class="font-semibold uppercase text-stone-600">{{ $row->metode }} <span class="text-xs font-normal text-stone-400">({{ $row->jumlah }}x)</span></span>
                        <span class="font-bold text-stone-800">Rp {{ number_format((float) $row->total, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-stone-500">Belum ada pembayaran berhasil.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="mt-6 rounded-none border border-stone-200 bg-white p-5">
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Deteksi Stok Mengendap (&gt;30 Hari)</h2>
        <span class="rounded-sm bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">{{ $aging->count() }} item</span>
    </div>
    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-100 text-sm">
            <thead class="text-left text-xs font-bold uppercase tracking-wide text-stone-400">
                <tr>
                    <th class="py-2 pr-3">SKU</th>
                    <th class="py-2 pr-3">Produk</th>
                    <th class="py-2 pr-3">Bal</th>
                    <th class="py-2 pr-3">Tgl Rilis</th>
                    <th class="py-2 pr-3">Harga</th>
                    <th class="py-2 pr-3">Rekomendasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($aging as $item)
                    <tr>
                        <td class="py-2 pr-3 font-mono text-xs text-stone-500">{{ $item->kode_sku }}</td>
                        <td class="py-2 pr-3 font-semibold text-stone-800">{{ $item->nama_produk }}</td>
                        <td class="py-2 pr-3 text-stone-500">{{ $item->bal->kode_bal ?? '-' }}</td>
                        <td class="py-2 pr-3 text-stone-500">{{ $item->tanggal_rilis?->translatedFormat('d M Y') ?? $item->created_at->translatedFormat('d M Y') }}</td>
                        <td class="py-2 pr-3 font-semibold">Rp {{ number_format((float) $item->harga_jual, 0, ',', '.') }}</td>
                        <td class="py-2 pr-3"><span class="rounded-sm bg-amber-100 px-2 py-1 text-[11px] font-bold text-amber-700">Diskon cuci gudang</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-stone-500">Tidak ada stok mengendap. Inventaris sehat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
