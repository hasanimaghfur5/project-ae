@extends('layouts.app')

@section('title', 'Katalog Thrift')

@section('content')
<section class="grid items-stretch gap-8 lg:grid-cols-[1.15fr_0.85fr]">
    <div class="reveal flex flex-col justify-center py-4 lg:py-10">
        <p class="eyebrow flex items-center gap-3">
            <span class="h-px w-10 bg-clay-500"></span>
            Thrift Atelier · Est. 2026
        </p>
        <h1 class="mt-5 text-[clamp(3rem,8vw,6.5rem)] font-semibold leading-[0.92] text-ink">
            Curated<br><span class="italic text-brand-700">secondhand</span><br>one of one.
        </h1>
        <p class="mt-6 max-w-md text-sm leading-relaxed text-stone-600">
            Setiap potong pakaian dikurasi, dicuci, diukur aktual, dan diberi satu kode SKU unik.
            Temukan item yang tidak akan Anda temui dua kali.
        </p>
        <div class="mt-8 flex flex-wrap items-center gap-3">
            <a href="#katalog" class="btn-primary">Jelajahi Katalog</a>
            @guest
                <a href="{{ route('register') }}" class="btn-outline">Buat Akun</a>
            @endguest
        </div>
        <dl class="mt-10 grid max-w-md grid-cols-3 gap-4 border-t border-ink/10 pt-6">
            <div>
                <dt class="eyebrow">Item</dt>
                <dd class="mt-1 font-display text-3xl text-ink">{{ $produk->total() }}</dd>
            </div>
            <div>
                <dt class="eyebrow">Kategori</dt>
                <dd class="mt-1 font-display text-3xl text-ink">{{ $kategori->count() }}</dd>
            </div>
            <div>
                <dt class="eyebrow">Booking</dt>
                <dd class="mt-1 font-display text-3xl text-ink">15<span class="ml-1 text-sm">mnt</span></dd>
            </div>
        </dl>
    </div>

    <div class="reveal relative min-h-[420px] overflow-hidden bg-brand-900">
        <img src="{{ \Illuminate\Support\Facades\Storage::url('hero.jpg') }}" alt="Kurasi pakaian preloved ReWear"
            class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-ink/65 via-ink/20 to-ink/95"></div>
        <div class="absolute inset-5 border border-cream/20"></div>
        <div class="relative flex h-full flex-col justify-between p-8 sm:p-10">
            <div class="flex items-start justify-between text-cream">
                <p class="eyebrow !text-cream/70">Drop / 001</p>
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-cream/70">Jumat, 19.00 WIB</p>
            </div>
            <div class="text-cream">
                <p class="font-display text-[clamp(3.5rem,9vw,7rem)] italic leading-[0.8]">01</p>
                <p class="mt-3 max-w-[16rem] font-display text-2xl leading-snug">Satu potong.<br>Satu pemilik berikutnya.</p>
                <div class="mt-6 flex items-center gap-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-cream/80">
                    <span class="h-px w-8 bg-clay-400"></span>
                    Kurasi · Laundry · Ukur
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mt-10 overflow-hidden border-y border-ink/10 py-3">
    <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-2 text-[10px] font-semibold uppercase tracking-[0.24em] text-stone-500">
        <span>Grade A / B / Minus</span><span class="text-clay-500">·</span>
        <span>Ukuran aktual cm</span><span class="text-clay-500">·</span>
        <span>Sterilisasi laundry</span><span class="text-clay-500">·</span>
        <span>Keterlacakan per bal</span><span class="text-clay-500">·</span>
        <span>Pembayaran aman</span>
    </div>
</div>

<div id="katalog" class="mt-12 grid gap-10 lg:grid-cols-[260px_1fr]">
    <aside class="h-fit lg:sticky lg:top-28">
        <div class="flex items-center justify-between border-b border-ink/10 pb-3">
            <h2 class="eyebrow">Filter</h2>
            <a href="{{ route('katalog.index') }}" class="text-[10px] font-semibold uppercase tracking-[0.16em] text-clay-600 hover:underline">Reset</a>
        </div>
        <form method="GET" action="{{ route('katalog.index') }}" class="mt-5 space-y-4">
            <div>
                <label for="q" class="field-label">Pencarian</label>
                <input id="q" type="text" name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Nama, merek, SKU" class="input mt-1.5">
            </div>
            <div>
                <label for="kategori" class="field-label">Kategori</label>
                <select id="kategori" name="kategori" class="input mt-1.5">
                    <option value="">Semua kategori</option>
                    @foreach($kategori as $item)
                        <option value="{{ $item->id_kategori }}" @selected(($filter['kategori'] ?? '') == $item->id_kategori)>{{ $item->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="merek" class="field-label">Merek</label>
                <select id="merek" name="merek" class="input mt-1.5">
                    <option value="">Semua merek</option>
                    @foreach($merek as $item)
                        <option value="{{ $item }}" @selected(($filter['merek'] ?? '') === $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="grade" class="field-label">Grade</label>
                <select id="grade" name="grade" class="input mt-1.5">
                    <option value="">Semua grade</option>
                    @foreach(['A', 'B', 'Minus'] as $grade)
                        <option value="{{ $grade }}" @selected(($filter['grade'] ?? '') === $grade)>Grade {{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="lebar_min" class="field-label">LD min (cm)</label>
                    <input id="lebar_min" type="number" step="0.1" name="lebar_min" value="{{ $filter['lebar_min'] ?? '' }}" class="input mt-1.5">
                </div>
                <div>
                    <label for="lebar_max" class="field-label">LD max (cm)</label>
                    <input id="lebar_max" type="number" step="0.1" name="lebar_max" value="{{ $filter['lebar_max'] ?? '' }}" class="input mt-1.5">
                </div>
            </div>
            <button class="btn-dark w-full">Terapkan Filter</button>
        </form>

        <div class="mt-6 border-t border-ink/10 pt-4 text-xs leading-relaxed text-stone-500">
            <p class="font-display text-lg text-ink">Butuh bantuan ukuran?</p>
            <p class="mt-1">LD = lebar dada, PB = panjang badan. Bandingkan dengan pakaian favorit Anda.</p>
        </div>
    </aside>

    <div>
        <div id="koleksi" aria-label="Koleksi produk">
            @include('katalog._koleksi')
        </div>
        <p id="koleksi-status" class="sr-only" role="status" aria-live="polite"></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const koleksi = document.getElementById('koleksi');
    const status = document.getElementById('koleksi-status');

    if (!koleksi) {
        return;
    }

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    let pengendali = null;

    async function muatKoleksi(url, tambahRiwayat = true) {
        if (pengendali) {
            pengendali.abort();
        }

        pengendali = new AbortController();
        const posisiScroll = window.scrollY;
        const pengendaliAktif = pengendali;

        koleksi.style.opacity = '0.45';
        koleksi.style.pointerEvents = 'none';
        koleksi.setAttribute('aria-busy', 'true');

        if (status) {
            status.textContent = 'Memuat koleksi...';
        }

        try {
            const respons = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                signal: pengendaliAktif.signal,
            });

            if (!respons.ok) {
                throw new Error('Gagal memuat koleksi.');
            }

            koleksi.innerHTML = await respons.text();
            koleksi.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
            window.scrollTo(0, posisiScroll);

            if (tambahRiwayat) {
                history.pushState({ koleksi: true }, '', url);
            }

            if (status) {
                status.textContent = 'Koleksi diperbarui.';
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.location.href = url;
            }
        } finally {
            if (pengendali === pengendaliAktif) {
                koleksi.style.opacity = '';
                koleksi.style.pointerEvents = '';
                koleksi.removeAttribute('aria-busy');
            }
        }
    }

    koleksi.addEventListener('mousedown', (event) => {
        if (event.button !== 0) {
            return;
        }

        if (event.target.closest('nav[aria-label="Navigasi halaman"] a[href]')) {
            event.preventDefault();
        }
    });

    koleksi.addEventListener('click', (event) => {
        const tautan = event.target.closest('nav[aria-label="Navigasi halaman"] a[href]');

        if (!tautan || !koleksi.contains(tautan)) {
            return;
        }

        event.preventDefault();
        muatKoleksi(tautan.href);
    });

    window.addEventListener('popstate', () => {
        if (document.getElementById('koleksi')) {
            muatKoleksi(window.location.href, false);
        }
    });
})();
</script>
@endpush
