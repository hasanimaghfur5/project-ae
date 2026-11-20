<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function index(Request $request): View|Response
    {
        $filter = $request->only(['q', 'kategori', 'grade', 'merek', 'lebar_min', 'lebar_max']);

        $produk = Produk::query()
            ->with(['kategori', 'bal'])
            ->where('stok', '>', 0)
            ->where(function ($query) {
                $query->whereNull('tanggal_rilis')
                    ->orWhereDate('tanggal_rilis', '<=', now()->toDateString());
            })
            ->when($filter['q'] ?? null, function ($query, $q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('nama_produk', 'like', "%{$q}%")
                        ->orWhere('merek', 'like', "%{$q}%")
                        ->orWhere('kode_sku', 'like', "%{$q}%");
                });
            })
            ->when($filter['kategori'] ?? null, fn ($query, $id) => $query->where('id_kategori', $id))
            ->when($filter['grade'] ?? null, fn ($query, $grade) => $query->where('grade', $grade))
            ->when($filter['merek'] ?? null, fn ($query, $merek) => $query->where('merek', $merek))
            ->when($filter['lebar_min'] ?? null, fn ($query, $min) => $query->where('lebar_dada_cm', '>=', $min))
            ->when($filter['lebar_max'] ?? null, fn ($query, $max) => $query->where('lebar_dada_cm', '<=', $max))
            ->latest('tanggal_rilis')
            ->paginate(12)
            ->withQueryString();

        $data = [
            'produk' => $produk,
            'filter' => $filter,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'merek' => Produk::query()->whereNotNull('merek')->distinct()->orderBy('merek')->pluck('merek'),
        ];

        if ($request->ajax()) {
            return response()->view('katalog._koleksi', $data);
        }

        return view('katalog.index', $data);
    }

    public function show(Produk $produk): View
    {
        $produk->load(['kategori', 'bal']);

        return view('katalog.show', compact('produk'));
    }
}
