@extends('layouts.app')

@section('title', 'Bal Baru')

@section('content')
<h1 class="text-4xl text-ink">Catat Bal Baru</h1>
<p class="mt-1 mb-5 text-sm text-stone-500">Pastikan kode bal unik agar setiap produk dapat dilacak ke modal asalnya (PA-02).</p>
@include('admin.bal._form')
@endsection
