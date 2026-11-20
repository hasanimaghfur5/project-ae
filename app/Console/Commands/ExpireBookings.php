<?php

namespace App\Console\Commands;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireBookings extends Command
{
    protected $signature = 'thrift:expire-bookings';

    protected $description = 'Melepas stok yang dipesan melewati batas pembayaran 15 menit';

    public function handle(): int
    {
        $expired = Pesanan::query()
            ->where('status_pesanan', 'pending')
            ->whereNotNull('batas_bayar')
            ->where('batas_bayar', '<=', now())
            ->with('detail')
            ->get();

        $jumlah = 0;

        foreach ($expired as $pesanan) {
            DB::transaction(function () use ($pesanan) {
                $pesanan->load('detail.produk');

                foreach ($pesanan->detail as $detail) {
                    $produk = Produk::whereKey($detail->id_produk)->lockForUpdate()->first();
                    if ($produk) {
                        $produk->increment('stok', $detail->jumlah);
                        $produk->update(['status_produk' => Produk::STATUS_TERSEDIA]);
                    }
                }

                $pesanan->update(['status_pesanan' => 'batal']);
                $pesanan->pembayaran()->where('status_bayar', 'pending')->update(['status_bayar' => 'gagal']);
            });

            $jumlah++;
        }

        $this->info("{$jumlah} pesanan kedaluwarsa diproses.");

        return self::SUCCESS;
    }
}
