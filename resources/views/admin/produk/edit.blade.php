@extends('layouts.app')

@section('title', 'Edit ' . $produk->nama_produk)

@section('content')
<h1 class="text-4xl text-ink">Edit Produk</h1>
<p class="mt-1 mb-5 text-sm text-stone-500">SKU {{ $produk->kode_sku }} tidak berubah agar keterlacakan tetap terjaga.</p>
@include('admin.produk._form')
@endsection
