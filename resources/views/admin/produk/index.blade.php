@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-4xl text-ink">Kurasi & Produk</h1>
        <p class="mt-1 text-sm text-stone-500">Setiap produk punya SKU unik dan stok per item. Label QR siap cetak.</p>
    </div>
    <a href="{{ route('produk.create') }}" class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">+ Produk Baru</a>
</div>

<form method="GET" class="mt-5 flex flex-wrap gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, SKU, merek"
        class="w-full max-w-xs rounded-sm border border-stone-300 px-3 py-2 text-sm">
    <select name="status" class="rounded-sm border border-stone-300 px-3 py-2 text-sm">
        <option value="">Semua status</option>
        @foreach(['tersedia' => 'Tersedia', 'dipesan' => 'Dipesan', 'terjual' => 'Terjual'] as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="bal" class="rounded-sm border border-stone-300 px-3 py-2 text-sm">
        <option value="">Semua bal</option>
        @foreach($bal as $item)
            <option value="{{ $item->id_bal }}" @selected((string) request('bal') === (string) $item->id_bal)>{{ $item->kode_bal }}</option>
        @endforeach
    </select>
    <button class="rounded-sm bg-stone-900 px-4 py-2 text-sm font-bold text-white hover:bg-stone-800">Filter</button>
    <a href="{{ route('produk.index') }}" class="rounded-sm border border-stone-200 px-4 py-2 text-sm font-bold text-stone-600 hover:bg-stone-100">Reset</a>
</form>

<div class="mt-5 overflow-hidden rounded-none border border-stone-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-200 text-sm">
            <thead class="bg-stone-50 text-left text-xs font-bold uppercase tracking-wide text-stone-500">
                <tr>
                    <th class="px-4 py-3">SKU</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">Bal</th>
                    <th class="px-4 py-3">Grade</th>
                    <th class="px-4 py-3">Ukuran</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Stok</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($produk as $item)
                    @php $badge = ['tersedia' => 'bg-emerald-100 text-emerald-700', 'dipesan' => 'bg-amber-100 text-amber-700', 'terjual' => 'bg-stone-200 text-stone-600'][$item->status_produk] ?? 'bg-stone-100'; @endphp
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-stone-500">{{ $item->kode_sku }}</td>
                        <td class="px-4 py-3">
                            <p class="font-bold text-stone-800">{{ $item->nama_produk }}</p>
                            <p class="text-xs text-stone-400">{{ $item->merek ?: 'Tanpa merek' }} · {{ $item->kategori->nama_kategori ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-stone-500">{{ $item->bal->kode_bal ?? '-' }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $item->grade }}</td>
                        <td class="px-4 py-3 text-xs text-stone-500">LD {{ rtrim(rtrim(number_format((float) $item->lebar_dada_cm, 1, ',', '.'), '0'), ',') }} / PB {{ rtrim(rtrim(number_format((float) $item->panjang_badan_cm, 1, ',', '.'), '0'), ',') }} cm</td>
                        <td class="px-4 py-3 font-semibold text-stone-800">Rp {{ number_format((float) $item->harga_jual, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-semibold text-stone-800">{{ $item->stok }}</td>
                        <td class="px-4 py-3"><span class="rounded-sm px-2 py-1 text-[11px] font-bold {{ $badge }}">{{ ucfirst($item->status_produk) }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('produk.label', $item) }}" class="rounded-sm border border-stone-200 px-2.5 py-1.5 text-xs font-semibold text-stone-600 hover:bg-stone-100">Label</a>
                                <a href="{{ route('produk.edit', $item) }}" class="rounded-sm border border-stone-200 px-2.5 py-1.5 text-xs font-semibold text-stone-600 hover:bg-stone-100">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-stone-500">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $produk->links() }}</div>
@endsection
