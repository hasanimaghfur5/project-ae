@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="mx-auto grid max-w-5xl overflow-hidden border border-stone-200 bg-white shadow-soft lg:grid-cols-2">
    <section class="p-8 sm:p-12">
        <p class="eyebrow">Mulai koleksi Anda</p>
        <h1 class="mt-2 text-4xl text-ink">Daftar akun pelanggan</h1>
        <p class="mt-2 text-sm text-stone-500">Pesan item unik langsung dari katalog ReWear.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="name" class="field-label">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="input mt-1.5">
            </div>
            <div>
                <label for="email" class="field-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input mt-1.5">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="field-label">Kata Sandi</label>
                    <input id="password" type="password" name="password" required class="input mt-1.5">
                </div>
                <div>
                    <label for="password_confirmation" class="field-label">Konfirmasi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="input mt-1.5">
                </div>
            </div>
            <div>
                <label for="no_telepon" class="field-label">No. Telepon</label>
                <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}" class="input mt-1.5">
            </div>
            <div>
                <label for="alamat" class="field-label">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" class="input mt-1.5">{{ old('alamat') }}</textarea>
            </div>
            <button class="btn-primary w-full">Daftar</button>
        </form>

        <p class="mt-6 text-sm text-stone-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-700 underline decoration-clay-500 underline-offset-4 hover:text-clay-600">Masuk</a>
        </p>
    </section>

    <section class="relative hidden overflow-hidden bg-brand-900 p-10 text-cream lg:block">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_15%,rgba(194,65,12,0.4),transparent_45%),radial-gradient(circle_at_15%_85%,rgba(79,149,122,0.45),transparent_50%)]"></div>
        <div class="relative flex h-full min-h-[560px] flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-display text-3xl font-semibold italic">ReWear</p>
                    <p class="mt-1 text-[9px] font-semibold uppercase tracking-[0.3em] text-cream/60">Thrift Atelier</p>
                </div>
                <p class="text-[10px] uppercase tracking-[0.2em] text-cream/60">Member / 2026</p>
            </div>
            <div>
                <p class="font-display text-[clamp(4rem,12vw,7rem)] italic leading-[0.8]">01</p>
                <p class="mt-4 max-w-xs font-display text-3xl leading-snug">Satu item. Satu SKU. Satu kesempatan.</p>
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-cream/70">Daftar untuk mendapatkan akses katalog, penguncian stok otomatis, dan pelacakan pesanan.</p>
            </div>
            <div class="flex items-center gap-4 border-t border-cream/20 pt-5 text-[10px] uppercase tracking-[0.18em] text-cream/60">
                <span>Kurasi</span><span class="h-px w-6 bg-clay-400"></span><span>Laundry</span><span class="h-px w-6 bg-clay-400"></span><span>Ukur</span>
            </div>
        </div>
    </section>
</div>
@endsection
