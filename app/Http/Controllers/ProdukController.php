<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\PemasokBal;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(Request $request): View
    {
        $produk = Produk::query()
            ->with(['kategori', 'bal'])
            ->when($request->query('q'), function ($query, $q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('kode_sku', 'like', "%{$q}%")
                        ->orWhere('merek', 'like', "%{$q}%");
                });
            })
            ->when($request->query('status'), fn ($query, $status) => $query->where('status_produk', $status))
            ->when($request->query('bal'), fn ($query, $id) => $query->where('id_bal', $id))
            ->latest('id_produk')
            ->paginate(15)
            ->withQueryString();

        return view('admin.produk.index', [
            'produk' => $produk,
            'bal' => PemasokBal::orderBy('kode_bal')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.produk.create', [
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'bal' => PemasokBal::orderBy('kode_bal')->get(),
            'selectedBal' => $request->query('bal'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduk($request);

        $data['kode_sku'] = Produk::generateSku((int) $data['id_bal']);
        $data['status_produk'] = (int) $data['stok'] > 0 ? Produk::STATUS_TERSEDIA : Produk::STATUS_TERJUAL;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('produk', 'public');
        }

        $produk = Produk::create($data);

        return redirect()->route('produk.show', $produk)
            ->with('status', "Produk dibuat dengan SKU {$produk->kode_sku}.");
    }

    public function show(Produk $produk): View
    {
        $produk->load(['kategori', 'bal', 'detailPesanan.pesanan']);

        return view('admin.produk.show', compact('produk'));
    }

    public function edit(Produk $produk): View
    {
        return view('admin.produk.edit', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'bal' => PemasokBal::orderBy('kode_bal')->get(),
        ]);
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $this->validateProduk($request);

        if ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $data['foto'] = $request->file('foto')->store('produk', 'public');
        }

        if ((int) $data['stok'] > 0) {
            $data['status_produk'] = Produk::STATUS_TERSEDIA;
        } elseif ($produk->status_produk === Produk::STATUS_TERSEDIA) {
            $data['status_produk'] = Produk::STATUS_TERJUAL;
        }

        $produk->update($data);

        return redirect()->route('produk.show', $produk)->with('status', 'Produk diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }
        $produk->delete();

        return redirect()->route('produk.index')->with('status', 'Produk dihapus.');
    }

    public function label(Produk $produk): View
    {
        $produk->load('bal');

        return view('admin.produk.label', compact('produk'));
    }

    private function validateProduk(Request $request): array
    {
        return $request->validate([
            'id_bal' => ['required', 'exists:pemasok_bal,id_bal'],
            'id_kategori' => ['required', 'exists:kategori,id_kategori'],
            'nama_produk' => ['required', 'string', 'max:150'],
            'merek' => ['nullable', 'string', 'max:80'],
            'grade' => ['required', 'in:A,B,Minus'],
            'lebar_dada_cm' => ['required', 'numeric', 'min:0'],
            'panjang_badan_cm' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0', 'max:9999'],
            'tanggal_rilis' => ['nullable', 'date'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ]);
    }
}
