@extends('layouts.app')

@section('title', 'Nota POS-' . str_pad((string) $pesanan->id_pesanan, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="no-print mb-4 flex flex-wrap items-center justify-between gap-2">
    <a href="{{ route('pos.index') }}" class="text-sm font-semibold text-stone-500 hover:text-brand-700">← Kembali ke POS</a>
    <button onclick="window.print()" class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">Cetak Nota</button>
</div>

<div class="mx-auto max-w-md">
    <div class="print-area rounded-none border border-stone-200 bg-white p-6">
        <div class="text-center">
            <p class="text-lg font-extrabold text-stone-900">ReWear Thrift</p>
            <p class="text-xs text-stone-500">Jl. Merdeka No. 1, Jakarta · 0812-0000-0001</p>
            <p class="mt-2 font-mono text-xs text-stone-400">Nota POS-{{ str_pad((string) $pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="mt-4 border-y border-dashed border-stone-300 py-3 text-xs text-stone-500">
            <div class="flex justify-between"><span>Waktu</span><span>{{ $pesanan->tanggal_pesanan?->translatedFormat('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>Kasir</span><span>{{ $pesanan->pengguna->name }}</span></div>
            <div class="flex justify-between"><span>Metode</span><span class="uppercase">{{ $pesanan->pembayaran->first()?->metode ?? '-' }}</span></div>
        </div>

        <div class="mt-3 space-y-2">
            @foreach($pesanan->detail as $detail)
                <div class="text-sm">
                    <p class="font-bold text-stone-800">{{ $detail->produk->nama_produk ?? 'Produk dihapus' }}</p>
                    <div class="flex justify-between text-xs text-stone-500">
                        <span>{{ $detail->produk->kode_sku ?? '-' }} · {{ $detail->jumlah }} × Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format((float) $detail->harga_satuan * $detail->jumlah, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex justify-between border-t border-stone-300 pt-3 text-base font-extrabold">
            <span>TOTAL</span>
            <span>Rp {{ number_format((float) $pesanan->total_harga, 0, ',', '.') }}</span>
        </div>

        <p class="mt-5 text-center text-xs text-stone-400">Terima kasih telah berbelanja. Barang bekas tetap berkualitas.</p>
    </div>
</div>
@endsection
