@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<h1 class="text-4xl text-ink">Konfirmasi Checkout</h1>
<p class="mt-1 text-sm text-stone-500">Setelah checkout, stok item dikunci selama 15 menit hingga pembayaran diverifikasi.</p>

<div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
    <div class="space-y-3">
        @foreach($items as $item)
            @php $produk = $item['produk']; @endphp
            <div class="flex items-center justify-between gap-4 rounded-none border border-stone-200 bg-white p-4">
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-stone-900">{{ $produk->nama_produk }}</p>
                    <p class="text-xs text-stone-500">{{ $produk->kode_sku }} · Grade {{ $produk->grade }} · LD {{ rtrim(rtrim(number_format((float) $produk->lebar_dada_cm, 1, ',', '.'), '0'), ',') }} cm</p>
                    <p class="mt-1 text-xs text-stone-500">{{ $item['jumlah'] }} × Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</p>
                </div>
                <p class="shrink-0 text-sm font-extrabold text-stone-900">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <aside class="h-fit rounded-none border border-stone-200 bg-white p-5">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Pembayaran</h2>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-stone-500">Subtotal</span><span class="font-bold">Rp {{ number_format((float) $subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Ongkos kirim</span><span class="font-bold">Rp {{ number_format((float) $ongkosKirim, 0, ',', '.') }}</span></div>
            <div class="flex justify-between border-t border-stone-200 pt-3 text-base"><span class="font-semibold">Total</span><span class="font-extrabold text-brand-700">Rp {{ number_format((float) $subtotal + (float) $ongkosKirim, 0, ',', '.') }}</span></div>
        </div>
        <div class="mt-4 rounded-sm bg-amber-50 p-3 text-xs text-amber-800">
            Stok dipotong saat pesanan dibuat. Batas pembayaran <span class="font-bold">15 menit</span>; lewat dari itu stok otomatis dikembalikan ke katalog.
        </div>
        <form method="POST" action="{{ route('checkout.store') }}" class="mt-4">
            @csrf
            <button class="w-full rounded-sm bg-brand-600 px-4 py-3 text-sm font-bold text-white hover:bg-brand-700">Buat Pesanan</button>
        </form>
        <a href="{{ route('cart.index') }}" class="mt-2 block text-center text-xs font-semibold text-stone-500 hover:text-stone-800">Kembali ke keranjang</a>
    </aside>
</div>
@endsection
