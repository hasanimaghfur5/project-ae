@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<h1 class="text-4xl text-ink">Keranjang Belanja</h1>
<p class="mt-1 text-sm text-stone-500">Stok akan dikunci 15 menit setelah checkout.</p>

@if($items->isEmpty())
    <div class="mt-6 rounded-none border border-dashed border-stone-300 bg-white p-10 text-center">
        <p class="text-sm text-stone-500">Keranjang masih kosong.</p>
        <a href="{{ route('katalog.index') }}" class="mt-3 inline-block rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">Lihat Katalog</a>
    </div>
@else
    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_340px]">
        <div class="space-y-3">
            @foreach($items as $item)
                @php $produk = $item['produk']; @endphp
                <div class="flex flex-col gap-4 rounded-none border border-stone-200 bg-white p-4 sm:flex-row sm:items-center">
                    <div class="h-24 w-20 shrink-0 overflow-hidden rounded-sm bg-stone-100">
                        @if($produk->foto)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($produk->foto) }}" alt="{{ $produk->nama_produk }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-xl font-black text-stone-400">R</div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-stone-900">{{ $produk->nama_produk }}</p>
                        <p class="text-xs text-stone-500">{{ $produk->kode_sku }} · {{ $produk->merek ?: 'Tanpa merek' }} · Grade {{ $produk->grade }}</p>
                        <p class="mt-1 text-sm font-extrabold text-brand-700">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }} <span class="text-xs font-normal text-stone-500">/ unit</span></p>
                        <p class="mt-0.5 text-xs text-stone-500">Stok tersisa {{ $produk->stok }} unit</p>

                        <form method="POST" action="{{ route('cart.update', $produk) }}" class="mt-2 flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label for="jumlah-{{ $produk->id_produk }}" class="text-xs font-semibold text-stone-600">Jumlah</label>
                            <input id="jumlah-{{ $produk->id_produk }}" type="number" name="jumlah" value="{{ $item['jumlah'] }}" min="1" max="{{ $produk->stok }}" required
                                class="w-20 rounded-sm border border-stone-300 px-2 py-1.5 text-sm">
                            <button class="rounded-sm border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-600 hover:bg-stone-100">Perbarui</button>
                        </form>
                    </div>
                    <div class="flex items-end justify-between gap-3 sm:flex-col sm:items-end">
                        <p class="text-sm font-extrabold text-stone-900">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</p>
                        <form method="POST" action="{{ route('cart.remove', $produk) }}">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-sm border border-stone-200 px-3 py-1.5 text-xs font-semibold text-stone-600 hover:bg-rose-50 hover:text-rose-600">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <aside class="h-fit rounded-none border border-stone-200 bg-white p-5">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Ringkasan</h2>
            <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-stone-500">Subtotal</span>
                <span class="font-bold text-stone-900">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
            </div>
            <div class="mt-2 flex items-center justify-between text-sm">
                <span class="text-stone-500">Ongkos kirim</span>
                <span class="font-bold text-stone-900">Rp 20.000</span>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-stone-200 pt-4">
                <span class="text-sm font-semibold text-stone-700">Total</span>
                <span class="text-lg font-extrabold text-brand-700">Rp {{ number_format((float) $total + 20000, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="mt-5 block rounded-sm bg-brand-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-brand-700">Lanjut ke Checkout</a>
        </aside>
    </div>
@endif
@endsection
