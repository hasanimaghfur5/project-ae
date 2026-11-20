<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\PemasokBal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BalController extends Controller
{
    public function index(Request $request): View
    {
        $bal = PemasokBal::query()
            ->withCount(['produk', 'biaya'])
            ->withSum('biaya', 'nominal')
            ->when($request->query('q'), function ($query, $q) {
                $query->where('kode_bal', 'like', "%{$q}%")
                    ->orWhere('nama_pemasok', 'like', "%{$q}%");
            })
            ->latest('tanggal_masuk')
            ->paginate(12)
            ->withQueryString();

        return view('admin.bal.index', compact('bal'));
    }

    public function create(): View
    {
        return view('admin.bal.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBal($request);
        PemasokBal::create($data);

        return redirect()->route('bal.index')->with('status', 'Bal baru berhasil dicatat.');
    }

    public function show(PemasokBal $bal): View
    {
        $bal->load(['produk.kategori', 'biaya']);

        $pendapatan = (float) DetailPesanan::query()
            ->whereHas('produk', fn ($query) => $query->where('id_bal', $bal->id_bal))
            ->whereHas('pesanan', fn ($query) => $query->whereIn('status_pesanan', ['dibayar', 'selesai']))
            ->sum('harga_satuan');

        $biaya = (float) $bal->biaya->sum('nominal');
        $laba = $pendapatan - (float) $bal->harga_beli - $biaya;

        return view('admin.bal.show', [
            'bal' => $bal,
            'pendapatan' => $pendapatan,
            'biayaOperasional' => $biaya,
            'laba' => $laba,
            'roi' => (float) $bal->harga_beli > 0 ? ($laba / (float) $bal->harga_beli) * 100 : 0,
        ]);
    }

    public function edit(PemasokBal $bal): View
    {
        return view('admin.bal.edit', compact('bal'));
    }

    public function update(Request $request, PemasokBal $bal): RedirectResponse
    {
        $bal->update($this->validateBal($request, $bal->id_bal));

        return redirect()->route('bal.show', $bal)->with('status', 'Data bal diperbarui.');
    }

    public function destroy(PemasokBal $bal): RedirectResponse
    {
        $bal->delete();

        return redirect()->route('bal.index')->with('status', 'Bal dihapus.');
    }

    public function storeBiaya(Request $request, PemasokBal $bal): RedirectResponse
    {
        $data = $request->validate([
            'jenis_biaya' => ['required', 'string', 'max:100'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'tanggal' => ['required', 'date'],
        ]);

        $bal->biaya()->create($data);

        return redirect()->route('bal.show', $bal)->with('status', 'Biaya operasional ditambahkan.');
    }

    public function destroyBiaya(PemasokBal $bal, int $biaya): RedirectResponse
    {
        $bal->biaya()->where('id_biaya', $biaya)->delete();

        return redirect()->route('bal.show', $bal)->with('status', 'Biaya operasional dihapus.');
    }

    private function validateBal(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'kode_bal' => ['required', 'string', 'max:30', 'unique:pemasok_bal,kode_bal'.($ignoreId ? ",{$ignoreId},id_bal" : '')],
            'nama_pemasok' => ['required', 'string', 'max:100'],
            'tanggal_masuk' => ['required', 'date'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'berat_kg' => ['required', 'numeric', 'min:0'],
            'jumlah_estimasi' => ['required', 'integer', 'min:0'],
        ]);
    }
}
