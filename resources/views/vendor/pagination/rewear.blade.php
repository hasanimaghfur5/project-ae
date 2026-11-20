@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3 border-t border-stone-200 pt-5">
        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-stone-500">
            Halaman {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </p>
        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 items-center border border-stone-200 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-stone-300">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 items-center border border-stone-300 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-stone-600 transition hover:border-ink hover:text-ink">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-9 w-9 items-center justify-center text-xs text-stone-400">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-9 w-9 items-center justify-center bg-ink text-xs font-bold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center border border-stone-300 text-xs font-semibold text-stone-600 transition hover:border-ink hover:text-ink">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 items-center border border-stone-300 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-stone-600 transition hover:border-ink hover:text-ink">Berikutnya</a>
            @else
                <span class="inline-flex h-9 items-center border border-stone-200 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-stone-300">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
