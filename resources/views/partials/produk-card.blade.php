@php
    $statusWarna = [
        'tersedia' => 'bg-brand-600 text-white',
        'dipesan' => 'bg-clay-500 text-white',
        'terjual' => 'bg-ink text-cream',
    ][$produk->status_produk] ?? 'bg-stone-200 text-stone-700';
@endphp
<article class="reveal group flex flex-col">
    <a href="{{ route('katalog.show', $produk) }}" class="relative block aspect-[3/4] overflow-hidden bg-stone-200">
        @if($produk->foto)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($produk->foto) }}" alt="{{ $produk->nama_produk }}"
                class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.04]" loading="lazy">
        @else
            <div class="flex h-full w-full flex-col items-center justify-center bg-[radial-gradient(circle_at_30%_20%,#F5F5F5,#E5E5E5_55%,#A3A3A3)] text-stone-700">
                <span class="font-display text-6xl italic">R</span>
                <span class="mt-2 text-[9px] font-semibold uppercase tracking-[0.28em] text-stone-600">Foto menyusul</span>
            </div>
        @endif
        <span class="absolute left-3 top-3 bg-cream/95 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.2em] text-ink">Grade {{ $produk->grade }}</span>
        <span class="absolute right-3 top-3 px-2.5 py-1 text-[9px] font-bold uppercase tracking-[0.2em] {{ $statusWarna }}">{{ ucfirst($produk->status_produk) }}</span>
        <span class="absolute inset-x-0 bottom-0 flex translate-y-full items-center justify-between bg-ink/90 px-4 py-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-cream transition duration-300 group-hover:translate-y-0">
            Lihat item
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12l-3.75 3.75M21 12H3" /></svg>
        </span>
    </a>

    <div class="flex flex-1 flex-col pt-4">
        <div class="flex items-center justify-between gap-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-stone-500">
            <span>{{ $produk->kategori->nama_kategori ?? '-' }}</span>
            <span>Bal {{ $produk->bal->kode_bal ?? '-' }} · Sisa {{ $produk->stok }}</span>
        </div>
        <a href="{{ route('katalog.show', $produk) }}" class="mt-1.5 font-display text-xl leading-tight text-ink transition hover:text-brand-700">{{ $produk->nama_produk }}</a>
        <p class="mt-1 text-xs text-stone-500">{{ $produk->merek ?: 'Tanpa merek' }} · LD {{ rtrim(rtrim(number_format((float) $produk->lebar_dada_cm, 1, ',', '.'), '0'), ',') }} / PB {{ rtrim(rtrim(number_format((float) $produk->panjang_badan_cm, 1, ',', '.'), '0'), ',') }} cm</p>

        <div class="mt-3 flex items-end justify-between gap-2">
            <p class="font-display text-2xl leading-none text-ink">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</p>
            @if($produk->stok > 0)
                @auth
                    <form method="POST" action="{{ route('cart.add', $produk) }}">
                        @csrf
                        <button class="inline-flex items-center gap-1.5 border-b border-ink pb-1 text-[10px] font-bold uppercase tracking-[0.16em] text-ink transition hover:border-clay-500 hover:text-clay-600" aria-label="Tambahkan {{ $produk->nama_produk }} ke keranjang">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Keranjang
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="border-b border-ink pb-1 text-[10px] font-bold uppercase tracking-[0.16em] text-ink transition hover:border-clay-500 hover:text-clay-600">Masuk</a>
                @endauth
            @endif
        </div>
    </div>
</article>
