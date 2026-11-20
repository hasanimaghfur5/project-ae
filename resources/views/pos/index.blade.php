@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-4xl text-ink">Point of Sale (POS)</h1>
        <p class="mt-1 text-sm text-stone-500">Pindai label QR atau ketik SKU untuk transaksi tatap muka di gerai.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="rounded-sm border border-stone-200 bg-white px-4 py-2.5 text-sm font-bold text-stone-700 hover:bg-stone-100">Dashboard</a>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[380px_1fr]">
    <div class="h-fit rounded-none border border-stone-200 bg-white p-6">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Transaksi Baru</h2>
        <form method="POST" action="{{ route('pos.store') }}" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="text-sm font-semibold text-stone-700">Kode SKU / Scan QR</label>
                <input type="text" name="kode_sku" id="kode_sku" value="{{ old('kode_sku', $sku) }}" required autofocus
                    placeholder="TS-B001-0001"
                    class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-3 font-mono text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100">
            </div>
            <div>
                <label class="text-sm font-semibold text-stone-700">Jumlah</label>
                <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required
                    class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="text-sm font-semibold text-stone-700">Metode Pembayaran</label>
                <select name="metode" class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
                    <option value="tunai" @selected(old('metode') === 'tunai')>Tunai</option>
                    <option value="qris" @selected(old('metode') === 'qris')>QRIS</option>
                </select>
            </div>
            <button class="w-full rounded-sm bg-brand-600 px-4 py-3 text-sm font-bold text-white hover:bg-brand-700">Proses Pembayaran</button>
        </form>
        <div class="mt-4 rounded-sm bg-amber-50 p-3 text-xs text-amber-800">
            Sistem menolak transaksi jika jumlah melebihi stok atau seluruh stok sedang <strong>dipesan</strong> pembeli online.
        </div>
    </div>

    <div class="rounded-none border border-stone-200 bg-white p-5">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Transaksi POS Terakhir</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-100 text-sm">
                <thead class="text-left text-xs font-bold uppercase tracking-wide text-stone-400">
                    <tr>
                        <th class="py-2 pr-3">Nota</th>
                        <th class="py-2 pr-3">Waktu</th>
                        <th class="py-2 pr-3">Item</th>
                        <th class="py-2 pr-3">Kasir</th>
                        <th class="py-2 pr-3">Total</th>
                        <th class="py-2 pr-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transaksi as $order)
                        <tr>
                            <td class="py-2 pr-3 font-bold text-stone-800">POS-{{ str_pad((string) $order->id_pesanan, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-2 pr-3 text-stone-500">{{ $order->tanggal_pesanan?->translatedFormat('d M H:i') }}</td>
                            <td class="py-2 pr-3 text-stone-600">{{ $order->detail->map(fn ($d) => ($d->produk->nama_produk ?? '-').' ×'.$d->jumlah)->join(', ') }}</td>
                            <td class="py-2 pr-3 text-stone-500">{{ $order->pengguna->name }}</td>
                            <td class="py-2 pr-3 font-semibold">Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                            <td class="py-2 pr-3 text-right"><a href="{{ route('pos.nota', $order) }}" class="rounded-sm border border-stone-200 px-2.5 py-1.5 text-xs font-semibold text-stone-600 hover:bg-stone-100">Nota</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-stone-500">Belum ada transaksi POS.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
