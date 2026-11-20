@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-4xl text-ink">Pesanan</h1>
        <p class="mt-1 text-sm text-stone-500">{{ auth()->user()->isStaf() ? 'Seluruh transaksi lintas kanal website dan POS.' : 'Riwayat pembelian Anda di ReWear.' }}</p>
    </div>
</div>

<div class="mt-6 overflow-hidden rounded-none border border-stone-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">No. Pesanan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Kanal</th>
                    <th class="px-4 py-3">Item</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($pesanan as $order)
                    @php
                        $label = ['pending' => 'bg-amber-100 text-amber-700', 'dibayar' => 'bg-emerald-100 text-emerald-700', 'selesai' => 'bg-brand-100 text-brand-700', 'batal' => 'bg-rose-100 text-rose-700'][$order->status_pesanan] ?? 'bg-stone-100 text-stone-600';
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-bold text-stone-800">#{{ str_pad((string) $order->id_pesanan, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $order->tanggal_pesanan?->translatedFormat('d M Y H:i') }}</td>
                        <td class="px-4 py-3 uppercase text-stone-500">{{ $order->kanal }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $order->detail->count() }} item</td>
                        <td class="px-4 py-3 font-bold text-stone-900">Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3"><span class="rounded-sm px-2 py-1 text-xs font-bold {{ $label }}">{{ ucfirst($order->status_pesanan) }}</span></td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pesanan.show', $order) }}" class="rounded-sm border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-600 hover:bg-stone-100">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-stone-500">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">{{ $pesanan->links() }}</div>
@endsection
