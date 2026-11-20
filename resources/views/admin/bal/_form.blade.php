@php $editing = isset($bal); @endphp
<form method="POST" action="{{ $editing ? route('bal.update', $bal) : route('bal.store') }}" class="space-y-4 rounded-none border border-stone-200 bg-white p-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-stone-700">Kode Bal</label>
            <input type="text" name="kode_bal" value="{{ old('kode_bal', $bal->kode_bal ?? '') }}" required placeholder="Contoh: B003"
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Nama Pemasok</label>
            <input type="text" name="nama_pemasok" value="{{ old('nama_pemasok', $bal->nama_pemasok ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', isset($bal) ? $bal->tanggal_masuk?->toDateString() : now()->toDateString()) }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Harga Beli (Rp)</label>
            <input type="number" step="0.01" min="0" name="harga_beli" value="{{ old('harga_beli', $bal->harga_beli ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Berat (kg)</label>
            <input type="number" step="0.01" min="0" name="berat_kg" value="{{ old('berat_kg', $bal->berat_kg ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Estimasi Jumlah Item</label>
            <input type="number" min="0" name="jumlah_estimasi" value="{{ old('jumlah_estimasi', $bal->jumlah_estimasi ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none">
        </div>
    </div>

    <div class="flex gap-2">
        <button class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">{{ $editing ? 'Simpan Perubahan' : 'Simpan Bal' }}</button>
        <a href="{{ route('bal.index') }}" class="rounded-sm border border-stone-200 px-4 py-2.5 text-sm font-bold text-stone-600 hover:bg-stone-100">Batal</a>
    </div>
</form>
