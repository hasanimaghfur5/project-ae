<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->keranjang($request);
        $items = $this->items($cart);

        return view('cart.index', [
            'items' => $items,
            'total' => $items->sum(fn (array $item) => $item['subtotal']),
        ]);
    }

    public function add(Request $request, Produk $produk): RedirectResponse
    {
        if (! $produk->isTersedia()) {
            return back()->with('error', 'Stok produk ini sedang habis.');
        }

        $cart = $this->keranjang($request);
        $jumlah = ($cart[$produk->id_produk] ?? 0) + 1;

        if ($jumlah > $produk->stok) {
            return back()->with('error', "Stok tersisa {$produk->stok} unit untuk produk ini.");
        }

        $cart[$produk->id_produk] = $jumlah;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        if ((int) $data['jumlah'] > $produk->stok) {
            return back()->with('error', "Stok tersisa {$produk->stok} unit untuk produk ini.");
        }

        $cart = $this->keranjang($request);
        $cart[$produk->id_produk] = (int) $data['jumlah'];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Jumlah produk diperbarui.');
    }

    public function remove(Request $request, Produk $produk): RedirectResponse
    {
        $cart = $this->keranjang($request);
        unset($cart[$produk->id_produk]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Produk dihapus dari keranjang.');
    }

    /**
     * @param  array<int, int>  $cart
     * @return Collection<int, array{produk: Produk, jumlah: int, subtotal: float}>
     */
    private function items(array $cart): Collection
    {
        if ($cart === []) {
            return collect();
        }

        return Produk::with(['kategori', 'bal'])
            ->whereIn('id_produk', array_keys($cart))
            ->get()
            ->map(function (Produk $produk) use ($cart): array {
                $jumlah = $cart[$produk->id_produk];

                return [
                    'produk' => $produk,
                    'jumlah' => $jumlah,
                    'subtotal' => (float) $produk->harga_jual * $jumlah,
                ];
            })
            ->values();
    }

    /**
     * @return array<int, int>
     */
    private function keranjang(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $hasil = [];

        if (array_is_list($cart)) {
            foreach ($cart as $id) {
                $hasil[(int) $id] = 1;
            }

            return $hasil;
        }

        foreach ($cart as $id => $jumlah) {
            $hasil[(int) $id] = max(1, (int) $jumlah);
        }

        return $hasil;
    }
}
