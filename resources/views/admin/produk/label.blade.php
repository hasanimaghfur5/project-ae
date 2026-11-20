@extends('layouts.app')

@section('title', 'Label ' . $produk->kode_sku)

@section('content')
<div class="no-print mb-4 flex flex-wrap items-center justify-between gap-2">
    <a href="{{ route('produk.show', $produk) }}" class="text-sm font-semibold text-stone-500 hover:text-brand-700">← Detail produk</a>
    <div class="flex gap-2">
        <button onclick="window.print()" class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">Cetak Label</button>
        <a href="{{ route('produk.index') }}" class="rounded-sm border border-stone-200 bg-white px-4 py-2.5 text-sm font-bold text-stone-700 hover:bg-stone-100">Kembali</a>
    </div>
</div>

<div class="mx-auto max-w-sm">
    <div class="print-area rounded-none border-2 border-dashed border-stone-300 bg-white p-5">
        <div class="flex items-start justify-between gap-3 border-b border-stone-200 pb-3">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-brand-600">ReWear Thrift</p>
                <p class="mt-1 font-mono text-sm font-bold text-stone-900">{{ $produk->kode_sku }}</p>
            </div>
            <div id="qrcode" class="h-20 w-20"></div>
        </div>
        <div class="mt-3">
            <p class="text-sm font-extrabold text-stone-900">{{ $produk->nama_produk }}</p>
            <p class="text-xs text-stone-500">{{ $produk->merek ?: 'Tanpa merek' }} · {{ $produk->kategori->nama_kategori ?? '-' }}</p>
            <p class="mt-2 text-xs text-stone-600">LD {{ $produk->lebar_dada_cm }} cm · PB {{ $produk->panjang_badan_cm }} cm</p>
            <div class="mt-2 flex items-center justify-between">
                <span class="rounded-md bg-stone-100 px-2 py-0.5 text-[11px] font-bold text-stone-700">Grade {{ $produk->grade }}</span>
                <span class="text-sm font-extrabold text-brand-700">Rp {{ number_format((float) $produk->harga_jual, 0, ',', '.') }}</span>
            </div>
            <p class="mt-2 text-[10px] text-stone-400">Bal asal: {{ $produk->bal->kode_bal ?? '-' }} · {{ $produk->bal->nama_pemasok ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('qrcode'), {
        text: '{{ $produk->kode_sku }}',
        width: 80,
        height: 80,
        correctLevel: QRCode.CorrectLevel.M,
    });
</script>
@endpush
