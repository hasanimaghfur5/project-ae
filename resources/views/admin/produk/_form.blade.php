@php $editing = isset($produk); @endphp
<form method="POST" action="{{ $editing ? route('produk.update', $produk) : route('produk.store') }}" enctype="multipart/form-data" class="space-y-4 rounded-none border border-stone-200 bg-white p-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-stone-700">Bal Asal</label>
            <select name="id_bal" required class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
                <option value="">Pilih bal</option>
                @foreach($bal as $item)
                    <option value="{{ $item->id_bal }}" @selected(old('id_bal', $produk->id_bal ?? $selectedBal ?? '') == $item->id_bal)>
                        {{ $item->kode_bal }} — {{ $item->nama_pemasok }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Kategori</label>
            <select name="id_kategori" required class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
                <option value="">Pilih kategori</option>
                @foreach($kategori as $item)
                    <option value="{{ $item->id_kategori }}" @selected(old('id_kategori', $produk->id_kategori ?? '') == $item->id_kategori)>{{ $item->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-semibold text-stone-700">Nama Produk</label>
            <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Merek</label>
            <input type="text" name="merek" value="{{ old('merek', $produk->merek ?? '') }}"
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Grade Kondisi</label>
            <select name="grade" required class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
                @foreach(['A' => 'Grade A (prima)', 'B' => 'Grade B (minus minor)', 'Minus' => 'Minus / reject'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('grade', $produk->grade ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Lebar Dada (cm)</label>
            <input type="number" step="0.1" min="0" name="lebar_dada_cm" value="{{ old('lebar_dada_cm', $produk->lebar_dada_cm ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Panjang Badan (cm)</label>
            <input type="number" step="0.1" min="0" name="panjang_badan_cm" value="{{ old('panjang_badan_cm', $produk->panjang_badan_cm ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Harga Jual (Rp)</label>
            <input type="number" step="0.01" min="0" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual ?? '') }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Stok</label>
            <input type="number" min="0" max="9999" name="stok" value="{{ old('stok', $produk->stok ?? 1) }}" required
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-stone-400">Jumlah unit yang tersedia untuk dijual.</p>
        </div>
        <div>
            <label class="text-sm font-semibold text-stone-700">Tanggal Rilis Katalog</label>
            <input type="date" name="tanggal_rilis" value="{{ old('tanggal_rilis', isset($produk) ? $produk->tanggal_rilis?->toDateString() : now()->toDateString()) }}"
                class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="text-sm font-semibold text-stone-700">Foto Produk</label>
            <input type="file" name="foto" accept="image/*" class="mt-1 w-full rounded-sm border border-stone-300 px-3 py-2.5 text-sm file:mr-3 file:rounded-sm file:border-0 file:bg-stone-100 file:px-3 file:py-1.5 file:text-xs file:font-bold">
            @if($editing && $produk->foto)
                <p class="mt-2 text-xs text-stone-500">Foto saat ini tersimpan. Unggah file baru untuk mengganti.</p>
            @else
                <p class="mt-2 text-xs text-stone-400">Opsional. Maksimal 4 MB.</p>
            @endif
        </div>
    </div>

    <div class="flex gap-2">
        <button class="rounded-sm bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">{{ $editing ? 'Simpan Perubahan' : 'Simpan & Generate SKU' }}</button>
        <a href="{{ route('produk.index') }}" class="rounded-sm border border-stone-200 px-4 py-2.5 text-sm font-bold text-stone-600 hover:bg-stone-100">Batal</a>
    </div>
</form>
