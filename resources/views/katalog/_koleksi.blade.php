<div class="mb-6 flex flex-wrap items-end justify-between gap-3 border-b border-ink/10 pb-4">
    <div>
        <h2 class="font-display text-3xl text-ink">Koleksi Tersedia</h2>
        <p class="mt-1 text-xs text-stone-500">{{ $produk->total() }} item · stok tersinkron real-time</p>
    </div>
    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-stone-400">1 item = 1 SKU</p>
</div>

@if($produk->isEmpty())
    <div class="border border-dashed border-stone-300 bg-white/60 p-12 text-center">
        <p class="font-display text-2xl text-ink">Tidak ada item yang cocok</p>
        <p class="mt-2 text-sm text-stone-500">Coba longgarkan filter ukuran atau kategori.</p>
        <a href="{{ route('katalog.index') }}" class="btn-outline mt-5">Reset Filter</a>
    </div>
@else
    <div class="grid grid-cols-2 gap-x-5 gap-y-10 sm:grid-cols-3 xl:grid-cols-4">
        @foreach($produk as $item)
            @include('partials.produk-card', ['produk' => $item])
        @endforeach
    </div>
    <div class="mt-10">{{ $produk->links() }}</div>
@endif
