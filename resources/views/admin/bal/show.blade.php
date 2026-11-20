@extends('layouts.app')

@section('title', 'Bal ' . $bal->kode_bal)

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
    <a href="{{ route('bal.index') }}" class="text-sm font-semibold text-stone-500 hover:text-brand-700">← Semua bal</a>
    <div class="flex gap-2">
        <a href="{{ route('produk.create', ['bal' => $bal->id_bal]) }}" class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">+ Produk dari Bal Ini</a>
        <a href="{{ route('bal.edit', $bal) }}" class="rounded-sm border border-stone-200 bg-white px-4 py-2.5 text-sm font-bold text-stone-700 hover:bg-stone-100">Edit Bal</a>
    </div>
</div>

<div class="rounded-none border border-stone-200 bg-white p-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-brand-600">Bal {{ $bal->kode_bal }}</p>
            <h1 class="mt-1 text-4xl text-ink">{{ $bal->nama_pemasok }}</h1>
            <p class="text-sm text-stone-500">Masuk {{ $bal->tanggal_masuk?->translatedFormat('d F Y') }} · {{ $bal->berat_kg }} kg · estimasi {{ $bal->jumlah_estimasi }} item</p>
        </div>
        <div class="text-right">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Harga Beli</p>
            <p class="text-xl font-extrabold text-stone-900">Rp {{ number_format((float) $bal->harga_beli, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-sm bg-stone-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Pendapatan</p>
            <p class="mt-1 text-lg font-extrabold text-stone-900">Rp {{ number_format($pendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-sm bg-stone-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">Biaya Operasional</p>
            <p class="mt-1 text-lg font-extrabold text-stone-900">Rp {{ number_format($biayaOperasional, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-sm {{ $laba >= 0 ? 'bg-emerald-50' : 'bg-rose-50' }} p-4">
            <p class="text-xs font-semibold uppercase tracking-wide {{ $laba >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Laba Bersih</p>
            <p class="mt-1 text-lg font-extrabold {{ $laba >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">Rp {{ number_format($laba, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-sm bg-stone-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">ROI</p>
            <p class="mt-1 text-lg font-extrabold text-stone-900">{{ number_format($roi, 1, ',', '.') }}%</p>
        </div>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
    <div class="rounded-none border border-stone-200 bg-white p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Produk dalam Bal ({{ $bal->produk->count() }})</h2>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-100 text-sm">
                <thead class="text-left text-xs font-bold uppercase tracking-wide text-stone-400">
                    <tr><th class="py-2 pr-3">SKU</th><th class="py-2 pr-3">Produk</th><th class="py-2 pr-3">Grade</th><th class="py-2 pr-3">Harga</th><th class="py-2 pr-3">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($bal->produk as $item)
                        @php $badge = ['tersedia' => 'bg-emerald-100 text-emerald-700', 'dipesan' => 'bg-amber-100 text-amber-700', 'terjual' => 'bg-stone-200 text-stone-600'][$item->status_produk] ?? 'bg-stone-100'; @endphp
                        <tr>
                            <td class="py-2 pr-3 font-mono text-xs text-stone-500">{{ $item->kode_sku }}</td>
                            <td class="py-2 pr-3 font-semibold text-stone-800"><a href="{{ route('produk.show', $item) }}" class="hover:text-brand-700">{{ $item->nama_produk }}</a></td>
                            <td class="py-2 pr-3 text-stone-500">{{ $item->grade }}</td>
                            <td class="py-2 pr-3 font-semibold">Rp {{ number_format((float) $item->harga_jual, 0, ',', '.') }}</td>
                            <td class="py-2 pr-3"><span class="rounded-sm px-2 py-1 text-[11px] font-bold {{ $badge }}">{{ ucfirst($item->status_produk) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-stone-500">Belum ada produk pada bal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-none border border-stone-200 bg-white p-5">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Biaya Operasional</h2>
        <form method="POST" action="{{ route('bal.biaya.store', $bal) }}" class="mt-3 space-y-2">
            @csrf
            <input type="text" name="jenis_biaya" placeholder="Jenis biaya (mis. Laundry)" required
                class="w-full rounded-sm border border-stone-300 px-3 py-2 text-sm">
            <div class="grid grid-cols-2 gap-2">
                <input type="number" step="0.01" min="0" name="nominal" placeholder="Nominal" required
                    class="w-full rounded-sm border border-stone-300 px-3 py-2 text-sm">
                <input type="date" name="tanggal" value="{{ now()->toDateString() }}" required
                    class="w-full rounded-sm border border-stone-300 px-3 py-2 text-sm">
            </div>
            <button class="w-full rounded-sm bg-stone-900 px-4 py-2 text-sm font-bold text-white hover:bg-stone-800">Tambah Biaya</button>
        </form>

        <div class="mt-4 divide-y divide-stone-100">
            @forelse($bal->biaya as $biaya)
                <div class="flex items-center justify-between gap-2 py-2 text-sm">
                    <div>
                        <p class="font-semibold text-stone-800">{{ $biaya->jenis_biaya }}</p>
                        <p class="text-xs text-stone-400">{{ $biaya->tanggal?->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-stone-700">Rp {{ number_format((float) $biaya->nominal, 0, ',', '.') }}</span>
                        <form method="POST" action="{{ route('bal.biaya.destroy', [$bal, $biaya->id_biaya]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-bold text-rose-500 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="py-3 text-sm text-stone-500">Belum ada biaya tercatat.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
