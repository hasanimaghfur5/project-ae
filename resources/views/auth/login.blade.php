@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="mx-auto grid max-w-5xl overflow-hidden border border-stone-200 bg-white shadow-soft lg:grid-cols-2">
    <section class="relative hidden overflow-hidden bg-brand-900 p-10 text-cream lg:block">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(194,65,12,0.4),transparent_45%),radial-gradient(circle_at_85%_85%,rgba(79,149,122,0.45),transparent_50%)]"></div>
        <div class="relative flex h-full min-h-[520px] flex-col justify-between">
            <div>
                <p class="font-display text-3xl font-semibold italic">ReWear</p>
                <p class="mt-1 text-[9px] font-semibold uppercase tracking-[0.3em] text-cream/60">Thrift Atelier</p>
            </div>
            <blockquote>
                <p class="font-display text-4xl italic leading-tight">“Pakaian terbaik adalah yang sudah punya cerita.”</p>
                <p class="mt-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-cream/60">Kurasi · Kualitas · Keberlanjutan</p>
            </blockquote>
            <div class="flex items-center gap-4 border-t border-cream/20 pt-5 text-[10px] uppercase tracking-[0.18em] text-cream/60">
                <span>One item</span><span class="h-px w-6 bg-clay-400"></span><span>One SKU</span><span class="h-px w-6 bg-clay-400"></span><span>One owner</span>
            </div>
        </div>
    </section>

    <section class="p-8 sm:p-12">
        <p class="eyebrow">Selamat datang kembali</p>
        <h1 class="mt-2 text-4xl text-ink">Masuk ke akun</h1>
        <p class="mt-2 text-sm text-stone-500">Kelola pesanan dan lanjutkan belanja item unik.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="email" class="field-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input mt-1.5">
            </div>
            <div>
                <label for="password" class="field-label">Kata Sandi</label>
                <input id="password" type="password" name="password" required class="input mt-1.5">
            </div>
            <label class="flex items-center gap-2 text-xs text-stone-600">
                <input type="checkbox" name="remember" value="1" class="rounded-sm border-stone-300 text-brand-600 focus:ring-brand-500">
                Ingat saya
            </label>
            <button class="btn-primary w-full">Masuk</button>
        </form>

        <p class="mt-6 text-sm text-stone-500">
            Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-brand-700 underline decoration-clay-500 underline-offset-4 hover:text-clay-600">Daftar sebagai pelanggan</a>
        </p>

        <div class="mt-8 border-t border-stone-200 pt-5">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500">Akun demo · password: <span class="text-ink">password</span></p>
            <div class="mt-3 grid grid-cols-2 gap-2 text-[11px] text-stone-600">
                <p>owner@thrift.test</p><p class="text-clay-600">Owner</p>
                <p>admin@thrift.test</p><p class="text-clay-600">Admin</p>
                <p>kasir@thrift.test</p><p class="text-clay-600">Kasir</p>
                <p>pelanggan@thrift.test</p><p class="text-clay-600">Pelanggan</p>
            </div>
        </div>
    </section>
</div>
@endsection
