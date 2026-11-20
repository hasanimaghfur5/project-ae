@extends('layouts.app')

@section('title', 'Produk Baru')

@section('content')
<h1 class="text-4xl text-ink">Input Hasil Kurasi</h1>
<p class="mt-1 mb-5 text-sm text-stone-500">SKU dan label QR dibangkitkan otomatis setelah produk disimpan.</p>
@include('admin.produk._form')
@endsection
