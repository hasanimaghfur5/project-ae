<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $pesanan = Pesanan::query()
            ->with(['detail.produk', 'pembayaran'])
            ->when(! $user->isStaf(), fn ($query) => $query->where('id_pengguna', $user->id))
            ->latest('tanggal_pesanan')
            ->paginate(15);

        return view('pesanan.index', compact('pesanan'));
    }

    public function show(Request $request, Pesanan $pesanan): View
    {
        $user = $request->user();
        abort_unless($user->isStaf() || $pesanan->id_pengguna === $user->id, 403);

        $pesanan->load(['detail.produk', 'pembayaran', 'pengguna']);

        return view('pesanan.show', compact('pesanan'));
    }
}
