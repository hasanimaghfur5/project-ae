@extends('layouts.app')

@section('title', 'Pesanan #' . str_pad((string) $pesanan->id_pesanan, 5, '0', STR_PAD_LEFT))

@section('content')
@php
    $label = ['pending' => 'bg-amber-100 text-amber-700', 'dibayar' => 'bg-emerald-100 text-emerald-700', 'selesai' => 'bg-brand-100 text-brand-700', 'batal' => 'bg-rose-100 text-rose-700'][$pesanan->status_pesanan] ?? 'bg-stone-100 text-stone-600';
    $pembayaran = $pesanan->pembayaran->sortByDesc('id_pembayaran')->first();
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-2">
    <a href="{{ route('pesanan.index') }}" class="text-sm font-semibold text-stone-500 hover:text-brand-700">← Semua pesanan</a>
    <span class="rounded-sm px-3 py-1.5 text-xs font-bold {{ $label }}">{{ ucfirst($pesanan->status_pesanan) }}</span>
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    <div class="rounded-none border border-stone-200 bg-white p-6 print-area">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b border-stone-200 pb-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-brand-600">ReWear Thrift</p>
                <h1 class="mt-1 text-4xl text-ink">Pesanan #{{ str_pad((string) $pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-sm text-stone-500">{{ $pesanan->tanggal_pesanan?->translatedFormat('d F Y H:i') }} · Kanal {{ strtoupper($pesanan->kanal) }}</p>
            </div>
            <div class="text-right text-sm">
                <p class="font-bold text-stone-800">{{ $pesanan->pengguna->name }}</p>
                <p class="text-stone-500">{{ $pesanan->pengguna->email }}</p>
                <p class="text-stone-500">{{ $pesanan->pengguna->no_telepon }}</p>
            </div>
        </div>

        <div class="mt-4 divide-y divide-stone-100">
            @foreach($pesanan->detail as $detail)
                <div class="flex items-center justify-between gap-4 py-3">
                    <div>
                        <p class="text-sm font-bold text-stone-900">{{ $detail->produk->nama_produk ?? 'Produk dihapus' }}</p>
                        <p class="text-xs text-stone-500">{{ $detail->produk->kode_sku ?? '-' }} · Grade {{ $detail->produk->grade ?? '-' }} · {{ $detail->jumlah }} unit</p>
                    </div>
                    <p class="text-sm font-bold text-stone-900">Rp {{ number_format((float) $detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 space-y-2 border-t border-stone-200 pt-4 text-sm">
            <div class="flex justify-between"><span class="text-stone-500">Subtotal item</span><span class="font-semibold">Rp {{ number_format($pesanan->detail->sum(fn ($detail) => (float) $detail->harga_satuan * $detail->jumlah), 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Ongkos kirim</span><span class="font-semibold">Rp {{ number_format((float) $pesanan->ongkos_kirim, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-base"><span class="font-bold">Total</span><span class="font-extrabold text-brand-700">Rp {{ number_format((float) $pesanan->total_harga, 0, ',', '.') }}</span></div>
        </div>

        @if($pesanan->no_resi)
            <div class="mt-4 rounded-sm border border-stone-200 bg-stone-50 p-3 text-sm">
                <span class="font-semibold text-stone-700">No. Resi:</span> <span class="font-mono">{{ $pesanan->no_resi }}</span>
            </div>
        @endif
    </div>

    <aside class="h-fit space-y-4">
        <div class="rounded-none border border-stone-200 bg-white p-6">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Pembayaran</h2>
            <p class="mt-3 text-sm text-stone-500">Metode: <span class="font-bold uppercase text-stone-800">{{ $pembayaran->metode ?? '-' }}</span></p>
            <p class="mt-1 text-sm text-stone-500">Status: <span class="font-bold text-stone-800">{{ ucfirst($pembayaran->status_bayar ?? '-') }}</span></p>
            @if($pembayaran?->waktu_bayar)
                <p class="mt-1 text-sm text-stone-500">Dibayar: {{ $pembayaran->waktu_bayar->translatedFormat('d M Y H:i') }}</p>
            @endif

            @if($pesanan->status_pesanan === 'pending')
                <div class="mt-4 rounded-sm bg-amber-50 p-3 text-center">
                    <p class="text-xs font-semibold text-amber-700">Sisa waktu pembayaran</p>
                    <p id="countdown" class="mt-1 text-2xl font-extrabold text-amber-800">--:--</p>
                </div>
                <form method="POST" action="{{ route('pesanan.pay', $pesanan) }}" class="mt-4">
                    @csrf
                    <button class="w-full rounded-sm bg-brand-600 px-4 py-3 text-sm font-bold text-white hover:bg-brand-700">Bayar Sekarang (Simulasi QRIS)</button>
                </form>
                <p class="mt-2 text-center text-[11px] text-stone-400">Pembayaran disimulasikan untuk kebutuhan demo.</p>
            @elseif($pesanan->status_pesanan === 'dibayar')
                <div class="mt-4 rounded-sm bg-emerald-50 p-3 text-sm text-emerald-800">Pembayaran terverifikasi. Pesanan diteruskan ke pengemasan.</div>
            @elseif($pesanan->status_pesanan === 'batal')
                <div class="mt-4 rounded-sm bg-rose-50 p-3 text-sm text-rose-800">Pesanan dibatalkan karena melewati batas pembayaran.</div>
            @endif
        </div>

        <div class="no-print rounded-none border border-stone-200 bg-white p-6">
            <h2 class="text-sm font-extrabold uppercase tracking-wide text-stone-700">Bantuan</h2>
            <p class="mt-2 text-sm text-stone-500">Hubungi admin melalui WhatsApp di 0812-0000-0001 untuk konsultasi ukuran atau kendala pembayaran.</p>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
@if($pesanan->status_pesanan === 'pending' && $pesanan->batas_bayar)
<script>
    const deadline = new Date('{{ $pesanan->batas_bayar->toIso8601String() }}').getTime();
    const el = document.getElementById('countdown');

    function tick() {
        const diff = deadline - Date.now();
        if (diff <= 0) {
            el.textContent = 'Kedaluwarsa';
            el.classList.add('text-rose-600');
            return;
        }
        const m = Math.floor(diff / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        setTimeout(tick, 1000);
    }
    tick();
</script>
@endif
@endpush
