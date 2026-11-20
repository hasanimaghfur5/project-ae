@extends('layouts.app')

@section('title', 'Manajemen Bal')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-4xl text-ink">Manajemen Bal</h1>
        <p class="mt-1 text-sm text-stone-500">Catat pembelian bal segel, biaya operasional, dan keterlacakan modal per bal.</p>
    </div>
    <a href="{{ route('bal.create') }}" class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">+ Bal Baru</a>
</div>

<form method="GET" class="mt-5 flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode bal atau pemasok"
        class="w-full max-w-sm rounded-sm border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none">
    <button class="rounded-sm bg-stone-900 px-4 py-2 text-sm font-bold text-white hover:bg-stone-800">Cari</button>
</form>

<div class="mt-5 overflow-hidden rounded-none border border-stone-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">Kode Bal</th>
                    <th class="px-4 py-3">Pemasok</th>
                    <th class="px-4 py-3">Tanggal Masuk</th>
                    <th class="px-4 py-3">Harga Beli</th>
                    <th class="px-4 py-3">Biaya</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($bal as $item)
                    <tr>
                        <td class="px-4 py-3 font-bold text-stone-900"><a href="{{ route('bal.show', $item) }}" class="hover:text-brand-700">{{ $item->kode_bal }}</a></td>
                        <td class="px-4 py-3 text-stone-600">{{ $item->nama_pemasok }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $item->tanggal_masuk?->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 font-semibold text-stone-800">Rp {{ number_format((float) $item->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-stone-600">Rp {{ number_format((float) ($item->biaya_sum_nominal ?? 0), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $item->produk_count }} item</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bal.show', $item) }}" class="rounded-sm border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-600 hover:bg-stone-100">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-stone-500">Belum ada bal tercatat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $bal->links() }}</div>
@endsection
