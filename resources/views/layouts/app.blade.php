<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ReWear Thrift') — ReWear Thrift Atelier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#EFF6F2', 100: '#D8E9DF', 200: '#B3D3C2', 300: '#85B7A0',
                            400: '#4F957A', 500: '#2F7659', 600: '#1F5A42', 700: '#184A36',
                            800: '#143C2D', 900: '#0F2F24', 950: '#081E16',
                        },
                        clay: {
                            50: '#FDF3EC', 100: '#FAE1D0', 200: '#F4C3A4', 300: '#EC9D70',
                            400: '#E07642', 500: '#C2410C', 600: '#A6360A', 700: '#832C0A',
                            800: '#66240B', 900: '#531F0C',
                        },
                        stone: {
                            50: '#FAFAFA', 100: '#F5F5F5', 200: '#E5E5E5', 300: '#D4D4D4',
                            400: '#A3A3A3', 500: '#737373', 600: '#525252', 700: '#404040',
                            800: '#262626', 900: '#171717', 950: '#0A0A0A',
                        },
                        cream: '#FFFFFF',
                        ink: '#1B1A17',
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                    },
                    boxShadow: {
                        soft: '0 18px 60px -30px rgba(27, 26, 23, 0.28)',
                    },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        @layer base {
            html { scroll-behavior: smooth; }
            body { @apply bg-white text-ink antialiased; }
            h1 { @apply font-display font-semibold tracking-tight; }
            ::selection { @apply bg-clay-500 text-white; }
            :focus-visible { @apply outline-none ring-2 ring-clay-500 ring-offset-2 ring-offset-white; }
        }
        @layer components {
            .eyebrow { @apply text-[10px] font-semibold uppercase tracking-[0.28em] text-stone-500; }
            .btn { @apply inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] transition duration-200; }
            .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-full bg-brand-600 px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-white transition duration-200 hover:bg-brand-700; }
            .btn-dark { @apply inline-flex items-center justify-center gap-2 rounded-full bg-ink px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-white transition duration-200 hover:bg-stone-800; }
            .btn-clay { @apply inline-flex items-center justify-center gap-2 rounded-full bg-clay-500 px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-white transition duration-200 hover:bg-clay-600; }
            .btn-outline { @apply inline-flex items-center justify-center gap-2 rounded-full border border-ink/20 bg-transparent px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-ink transition duration-200 hover:border-ink hover:bg-ink hover:text-white; }
            .btn-ghost { @apply inline-flex items-center justify-center gap-2 rounded-full border border-stone-300 bg-white px-6 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-stone-700 transition duration-200 hover:bg-stone-100; }
            .card { @apply rounded-sm border border-stone-200 bg-white; }
            .input { @apply w-full rounded-sm border border-stone-300 bg-white px-4 py-3 text-sm text-ink placeholder:text-stone-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500; }
            .field-label { @apply text-[11px] font-semibold uppercase tracking-[0.16em] text-stone-600; }
            .nav-link { @apply rounded-full px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-stone-600 transition hover:bg-stone-200/60 hover:text-ink; }
            .table-head { @apply text-left text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500; }
            .reveal { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s cubic-bezier(.22,1,.36,1); }
            .reveal.is-visible { opacity: 1; transform: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .reveal { opacity: 1; transform: none; transition: none; }
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: none !important; }
        }
    </style>
    @stack('head')
</head>
<body class="min-h-screen">
    <div class="no-print bg-ink px-4 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.24em] text-stone-200">
        Gratis ongkir min. 2 item · New drop setiap Jumat · Satu item satu SKU
    </div>

    <header class="no-print sticky top-0 z-40 border-b border-ink/10 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <div class="flex items-center gap-8">
                <a href="{{ route('katalog.index') }}" class="group flex items-baseline gap-2">
                    <span class="font-display text-3xl font-semibold italic leading-none text-ink">ReWear</span>
                    <span class="hidden text-[9px] font-semibold uppercase tracking-[0.3em] text-stone-500 sm:inline">Thrift Atelier</span>
                </a>
                <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                    <a href="{{ route('katalog.index') }}" class="nav-link {{ request()->routeIs('katalog.*') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">Katalog</a>
                    @auth
                        @if(auth()->user()->isStaf())
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">Dashboard</a>
                            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">POS</a>
                            @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                                <a href="{{ route('bal.index') }}" class="nav-link {{ request()->routeIs('bal.*') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">Bal</a>
                                <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">Produk</a>
                                <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">Laporan</a>
                            @endif
                        @else
                            <a href="{{ route('pesanan.index') }}" class="nav-link {{ request()->routeIs('pesanan.*') ? 'text-ink underline decoration-clay-500 decoration-2 underline-offset-8' : '' }}">Pesanan Saya</a>
                        @endif
                    @endauth
                </nav>
            </div>

            <div class="flex items-center gap-2">
                @auth
                    @unless(auth()->user()->isStaf())
                        @php $cartCount = count(session('cart', [])); @endphp
                        <a href="{{ route('cart.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-ink/15 text-ink transition hover:bg-ink hover:text-cream" aria-label="Keranjang belanja">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12A2.25 2.25 0 0 1 18.632 23H5.368a2.25 2.25 0 0 1-2.243-2.493l1.263-12A2.25 2.25 0 0 1 6.629 6.5h10.742a2.25 2.25 0 0 1 2.235 2.007Z" />
                            </svg>
                            @if($cartCount > 0)
                                <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-clay-500 px-1 text-[10px] font-bold text-white">{{ $cartCount }}</span>
                            @endif
                        </a>
                    @endunless
                    <div class="hidden text-right sm:block">
                        <p class="text-xs font-semibold text-ink">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] uppercase tracking-[0.2em] text-clay-600">{{ auth()->user()->peran }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn-ghost !px-4 !py-2.5">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-dark !px-5 !py-2.5">Daftar</a>
                @endauth
            </div>
        </div>

        <div class="flex gap-1 overflow-x-auto border-t border-ink/10 px-4 py-2 lg:hidden">
            <a href="{{ route('katalog.index') }}" class="nav-link whitespace-nowrap">Katalog</a>
            @auth
                @if(auth()->user()->isStaf())
                    <a href="{{ route('dashboard') }}" class="nav-link whitespace-nowrap">Dashboard</a>
                    <a href="{{ route('pos.index') }}" class="nav-link whitespace-nowrap">POS</a>
                    @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
                        <a href="{{ route('bal.index') }}" class="nav-link whitespace-nowrap">Bal</a>
                        <a href="{{ route('produk.index') }}" class="nav-link whitespace-nowrap">Produk</a>
                        <a href="{{ route('laporan.index') }}" class="nav-link whitespace-nowrap">Laporan</a>
                    @endif
                @else
                    <a href="{{ route('pesanan.index') }}" class="nav-link whitespace-nowrap">Pesanan</a>
                    <a href="{{ route('cart.index') }}" class="nav-link whitespace-nowrap">Keranjang</a>
                @endif
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:py-14">
        @if(session('status'))
            <div class="mb-6 flex items-start gap-3 border-l-2 border-brand-600 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">
                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-600"></span>
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 flex items-start gap-3 border-l-2 border-clay-500 bg-clay-50 px-4 py-3 text-sm font-medium text-clay-800">
                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-clay-500"></span>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 border-l-2 border-clay-500 bg-clay-50 px-4 py-3 text-sm text-clay-900">
                <p class="font-semibold">Periksa kembali input Anda:</p>
                <ul class="mt-1 list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="no-print mt-16 bg-ink text-stone-300">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
            <div class="grid gap-10 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
                <div>
                    <p class="font-display text-4xl font-semibold italic text-cream">ReWear</p>
                    <p class="mt-1 text-[9px] font-semibold uppercase tracking-[0.3em] text-stone-500">Thrift Atelier</p>
                    <p class="mt-4 max-w-xs text-sm leading-relaxed text-stone-400">Pakaian bekas kurasi, satu potong satu cerita. Dikelola dengan arsitektur enterprise TOGAF ADM.</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-500">Jelajahi</p>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a class="text-stone-300 transition hover:text-cream" href="{{ route('katalog.index') }}">Katalog</a></li>
                        @auth
                            <li><a class="text-stone-300 transition hover:text-cream" href="{{ route('pesanan.index') }}">Pesanan</a></li>
                        @else
                            <li><a class="text-stone-300 transition hover:text-cream" href="{{ route('login') }}">Masuk</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-500">Layanan</p>
                    <ul class="mt-4 space-y-2 text-sm text-stone-400">
                        <li>Kurasi & grading</li>
                        <li>Laundry & sterilisasi</li>
                        <li>Pengukuran aktual</li>
                        <li>Booking stok 15 menit</li>
                    </ul>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-stone-500">Kontak</p>
                    <ul class="mt-4 space-y-2 text-sm text-stone-400">
                        <li>Jl. Merdeka No. 1, Jakarta</li>
                        <li>0812-0000-0001</li>
                        <li>halo@rewear.test</li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 flex flex-col items-start justify-between gap-2 border-t border-white/10 pt-6 text-[10px] uppercase tracking-[0.18em] text-stone-500 sm:flex-row sm:items-center">
                <p>© {{ date('Y') }} ReWear Thrift Atelier</p>
                <p>Tugas Arsitektur Enterprise · Universitas Bina Sarana Informatika</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const elements = document.querySelectorAll('.reveal');
            if (!elements.length) return;
            if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                elements.forEach(function (el) { el.classList.add('is-visible'); });
                return;
            }
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            elements.forEach(function (el) { observer.observe(el); });
        });
    </script>
    @stack('scripts')
</body>
</html>
