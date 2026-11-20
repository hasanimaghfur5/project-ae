@extends('layouts.app')

@section('title', 'Edit Bal ' . $bal->kode_bal)

@section('content')
<h1 class="text-4xl text-ink">Edit Bal {{ $bal->kode_bal }}</h1>
<p class="mt-1 mb-5 text-sm text-stone-500">Perubahan harga beli akan memengaruhi perhitungan margin bal.</p>
@include('admin.bal._form')
@endsection
