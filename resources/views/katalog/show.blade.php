@extends('layouts.app')

@section('title', $produk->nama_produk)

@section('content')
<nav class="mb-6 flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">
    <a href="{{ route('katalog.index') }}" class="hover:text-ink">Katalog</a>
    <span class="text-stone-300">/</span>
    <span class="text-stone-700">{{ $produk->kategori->nama_kategori ?? '-' }}</span>
    <span class="text-stone-300">/</span>
    <span class="font-mono text-stone-500">{{ $produk->kode_sku }}</span>
</nav>

<div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
    <div class="lg:sticky lg:top-28 lg:self-start">
        <div class="relative aspect-[3/4] overflow-hidden bg-stone-200">
            @if($produk->foto)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($produk->foto) }}" alt="{{ $produk->nama_produk }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full w-full flex-col items-center justify-center bg-[radial-gradient(circle_at_30%_20%,#F5F5F5,#E5E5E5_55%,#A3A3A3)] text-stone-700">
                    <span class="font-display text-8xl italic">R</span>
                    <span class="mt-3 text-[10px] font-semibold uppercase tracking-[0.3em] text-stone-600">Foto produk menyusul</span>
                </div>
            @endif
            <span class="absolute left-4 top-4 bg-cream/95 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-ink">Grade {{ $produk->grade }}</span>
        </div>
        <div class="mt-3 flex items-center justify-between text-[10px] uppercase tracking-[0.18em] text-stone-500">
            <span>Bal asal {{ $produk->bal->kode_bal ?? '-' }} · {{ $produk->bal->nama_pemasok ?? '-' }}</span>
            <span>{{ $produk->tanggal_rilis?->translatedFormat('d M Y') ?? '-' }}</span>
        </div>
    </div>

    <div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="bg-brand-50 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-brand-700">{{ $produk->kategori->nama_kategori ?? '-' }}</span>
            <span class="bg-stone-200 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-stone-700">Grade {{ $produk->grade }}</span>
            @php
                $badge = $produk->status_produk === 'tersedia'
                    ? 'bg-brand-600 text-white'
                    : ($produk->status_produk === 'dipesan' ? 'bg-clay-500 text-white' : 'bg-ink text-cream');
            @endphp
            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] {{ $badge }}">{{ ucfirst($produk->status_produk) }}</span>
        </div>

        <h1 class="mt-4 text-4xl leading-[0.98] text-ink sm:text-5xl">{{ $produk->nama_produk }}</h1>
        <p class="mt-2 text-sm text-stone-500">{{ $produk->merek ?: 'Tanpa merek' }} · {{ $produk->kode_sku }}</p>

        <div class="mt-6 flex items-end justify-between border-y border-ink/10 py-5">
            <div>
                <p class="eyebrow">Harga</p>
                <p class="mt-1 font-display text-4xl leading-none text-brand-700">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-[0.18em] text-stone-500">Sisa stok</p>
                <p class="mt-1 font-display text-2xl leading-none text-ink">{{ $produk->stok }} <span class="text-sm">unit</span></p>
            </div>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-px overflow-hidden border border-stone-200 bg-stone-200">
            <div class="bg-white p-4">
                <dt class="eyebrow">Lebar dada</dt>
                <dd class="mt-1 font-display text-2xl text-ink">{{ rtrim(rtrim(number_format((float) $produk->lebar_dada_cm, 1, ',', '.'), '0'), ',') }} <span class="text-sm">cm</span></dd>
            </div>
            <div class="bg-white p-4">
                <dt class="eyebrow">Panjang badan</dt>
                <dd class="mt-1 font-display text-2xl text-ink">{{ rtrim(rtrim(number_format((float) $produk->panjang_badan_cm, 1, ',', '.'), '0'), ',') }} <span class="text-sm">cm</span></dd>
            </div>
            <div class="bg-white p-4">
                <dt class="eyebrow">Kondisi</dt>
                <dd class="mt-1 font-display text-2xl text-ink">Grade {{ $produk->grade }}</dd>
            </div>
            <div class="bg-white p-4">
                <dt class="eyebrow">Asal bal</dt>
                <dd class="mt-1 font-display text-2xl text-ink">{{ $produk->bal->kode_bal ?? '-' }}</dd>
            </div>
            <div class="bg-white p-4">
                <dt class="eyebrow">Stok tersedia</dt>
                <dd class="mt-1 font-display text-2xl text-ink">{{ $produk->stok }} <span class="text-sm">unit</span></dd>
            </div>
            <div class="bg-white p-4">
                <dt class="eyebrow">Kode SKU</dt>
                <dd class="mt-2 font-mono text-sm font-bold text-ink">{{ $produk->kode_sku }}</dd>
            </div>
        </dl>

        <div class="mt-6 border-l-2 border-clay-500 bg-clay-50/60 px-4 py-3 text-xs leading-relaxed text-clay-900">
            Stok terbatas dan dipotong otomatis saat checkout. Pesanan yang belum dibayar dalam 15 menit akan dilepas kembali ke katalog.
        </div>

        @if($produk->stok > 0)
            @auth
                <form method="POST" action="{{ route('cart.add', $produk) }}" class="mt-6">
                    @csrf
                    <button class="btn-primary w-full">Tambahkan ke Keranjang</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-primary mt-6 w-full">Masuk untuk Membeli</a>
            @endauth
        @elseif($produk->status_produk === 'dipesan')
            <div class="mt-6 border border-clay-200 bg-clay-50 px-4 py-3 text-sm text-clay-800">
                Seluruh stok item ini sedang dikunci pembeli lain. Bila pembayaran kedaluwarsa, stok kembali tersedia.
            </div>
        @else
            <div class="mt-6 border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-600">
                Stok item ini sudah habis. Jelajahi katalog untuk menemukan item lain.
            </div>
        @endif

        <ul class="mt-8 grid gap-3 border-t border-ink/10 pt-6 text-xs text-stone-600 sm:grid-cols-3">
            <li class="flex items-start gap-2"><span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-600"></span>Sudah dicuci & disterilisasi</li>
            <li class="flex items-start gap-2"><span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-600"></span>Ukuran diukur aktual</li>
            <li class="flex items-start gap-2"><span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-brand-600"></span>Bisa dikembalikan bila tidak sesuai</li>
        </ul>
    </div>
</div>
@endsection
