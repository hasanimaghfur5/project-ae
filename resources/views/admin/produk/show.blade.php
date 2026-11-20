@extends('layouts.app')

@section('title', $produk->nama_produk)

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
    <a href="{{ route('produk.index') }}" class="text-sm font-semibold text-stone-500 hover:text-brand-700">← Data produk</a>
    <div class="flex gap-2">
        <a href="{{ route('produk.label', $produk) }}" class="rounded-sm bg-stone-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-stone-800">Cetak Label QR</a>
        <a href="{{ route('produk.edit', $produk) }}" class="rounded-sm border border-stone-200 bg-white px-4 py-2.5 text-sm font-bold text-stone-700 hover:bg-stone-100">Edit</a>
        <form method="POST" action="{{ route('produk.destroy', $produk) }}" onsubmit="return confirm('Hapus produk ini?')">
            @csrf
            @method('DELETE')
            <button class="rounded-sm border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-100">Hapus</button>
        </form>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-[320px_1fr]">
    <div class="overflow-hidden rounded-none border border-stone-200 bg-white">
        @if($produk->foto)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($produk->foto) }}" alt="{{ $produk->nama_produk }}" class="aspect-[4/5] w-full object-cover">
        @else
            <div class="flex aspect-[4/5] w-full flex-col items-center justify-center bg-gradient-to-br from-stone-100 to-stone-200 text-stone-400">
                <span class="text-5xl font-black">R</span>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="rounded-none border border-stone-200 bg-white p-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-sm bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700">{{ $produk->kategori->nama_kategori ?? '-' }}</span>
                <span class="rounded-sm bg-stone-100 px-2.5 py-1 text-xs font-bold text-stone-600">Grade {{ $produk->grade }}</span>
                @php $badge = ['tersedia' => 'bg-emerald-100 text-emerald-700', 'dipesan' => 'bg-amber-100 text-amber-700', 'terjual' => 'bg-stone-200 text-stone-600'][$produk->status_produk]; @endphp
                <span class="rounded-sm px-2.5 py-1 text-xs font-bold {{ $badge }}">{{ ucfirst($produk->status_produk) }}</span>
            </div>
            <h1 class="mt-3 text-4xl text-ink">{{ $produk->nama_produk }}</h1>
            <p class="mt-1 font-mono text-sm text-stone-500">{{ $produk->kode_sku }}</p>
            <p class="mt-3 text-2xl font-extrabold text-brand-700">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</p>

            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Merek</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->merek ?: '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Lebar Dada</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->lebar_dada_cm }} cm</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Panjang Badan</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->panjang_badan_cm }} cm</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Asal Bal</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->bal->kode_bal ?? '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Pemasok</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->bal->nama_pemasok ?? '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Tanggal Rilis</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->tanggal_rilis?->translatedFormat('d M Y') ?? '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-stone-400">Stok</dt><dd class="mt-0.5 font-bold text-stone-800">{{ $produk->stok }} unit</dd></div>
            </dl>
        </div>

        <div class="rounded-none border border-stone-200 bg-white p-6">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Riwayat Transaksi Item</h2>
            <div class="mt-3 divide-y divide-stone-100 text-sm">
                @forelse($produk->detailPesanan->sortByDesc('id_detail') as $detail)
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="font-semibold text-stone-800">Pesanan #{{ str_pad((string) $detail->id_pesanan, 5, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-xs text-stone-400">{{ $detail->pesanan?->tanggal_pesanan?->translatedFormat('d M Y H:i') }} · {{ ucfirst($detail->pesanan?->status_pesanan ?? '-') }} · {{ $detail->jumlah }} unit</p>
                        </div>
                        <span class="font-bold text-stone-700">Rp {{ number_format((float) $detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="py-3 text-stone-500">Item belum pernah masuk pesanan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
