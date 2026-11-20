<?php

namespace Database\Seeders;

use App\Models\BiayaBal;
use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\PemasokBal;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Pemilik Thrift',
            'email' => 'owner@thrift.test',
            'password' => Hash::make('password'),
            'peran' => 'owner',
            'no_telepon' => '081200000001',
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
        ]);

        User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@thrift.test',
            'password' => Hash::make('password'),
            'peran' => 'admin',
            'no_telepon' => '081200000002',
            'alamat' => 'Jl. Merdeka No. 2, Jakarta',
        ]);

        User::create([
            'name' => 'Kasir Toko',
            'email' => 'kasir@thrift.test',
            'password' => Hash::make('password'),
            'peran' => 'kasir',
            'no_telepon' => '081200000003',
            'alamat' => 'Jl. Merdeka No. 3, Jakarta',
        ]);

        $pelanggan = User::create([
            'name' => 'Pelanggan Demo',
            'email' => 'pelanggan@thrift.test',
            'password' => Hash::make('password'),
            'peran' => 'pelanggan',
            'no_telepon' => '081200000004',
            'alamat' => 'Jl. Melati No. 4, Bandung',
        ]);

        $kategori = collect([
            ['nama_kategori' => 'Atasan', 'deskripsi' => 'Kemeja, kaos, blouse, dan sweater.'],
            ['nama_kategori' => 'Bawahan', 'deskripsi' => 'Celana, jeans, dan rok.'],
            ['nama_kategori' => 'Outerwear', 'deskripsi' => 'Jaket, kardigan, dan coat.'],
            ['nama_kategori' => 'Dress', 'deskripsi' => 'Gaun dan terusan.'],
            ['nama_kategori' => 'Sepatu', 'deskripsi' => 'Sepatu bekas layak pakai.'],
            ['nama_kategori' => 'Aksesoris', 'deskripsi' => 'Tas, topi, dan ikat pinggang.'],
        ])->mapWithKeys(fn (array $item) => [
            $item['nama_kategori'] => Kategori::create($item),
        ]);

        $balSatu = PemasokBal::create([
            'kode_bal' => 'B001',
            'nama_pemasok' => 'Pemasok Bandung',
            'tanggal_masuk' => now()->subDays(40)->toDateString(),
            'harga_beli' => 2500000,
            'berat_kg' => 25,
            'jumlah_estimasi' => 100,
        ]);

        $balDua = PemasokBal::create([
            'kode_bal' => 'B002',
            'nama_pemasok' => 'Pemasok Jakarta',
            'tanggal_masuk' => now()->subDays(10)->toDateString(),
            'harga_beli' => 3000000,
            'berat_kg' => 30,
            'jumlah_estimasi' => 120,
        ]);

        BiayaBal::create([
            'id_bal' => $balSatu->id_bal,
            'jenis_biaya' => 'Laundry & Sterilisasi',
            'nominal' => 150000,
            'tanggal' => now()->subDays(38)->toDateString(),
        ]);

        BiayaBal::create([
            'id_bal' => $balDua->id_bal,
            'jenis_biaya' => 'Laundry & Sterilisasi',
            'nominal' => 180000,
            'tanggal' => now()->subDays(8)->toDateString(),
        ]);

        $produkData = [
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Kemeja Flanel Kotak', 'merek' => 'Uniqlo', 'grade' => 'A', 'lebar' => 52, 'panjang' => 68, 'harga' => 85000, 'rilis' => 36],
            ['bal' => $balSatu, 'kategori' => 'Bawahan', 'nama' => 'Jeans Straight Cut', 'merek' => 'Levis', 'grade' => 'B', 'lebar' => 42, 'panjang' => 100, 'harga' => 125000, 'rilis' => 36],
            ['bal' => $balSatu, 'kategori' => 'Outerwear', 'nama' => 'Jaket Varsity', 'merek' => 'H&M', 'grade' => 'A', 'lebar' => 54, 'panjang' => 66, 'harga' => 175000, 'rilis' => 35],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Kaos Basic Cotton', 'merek' => 'Uniqlo', 'grade' => 'A', 'lebar' => 50, 'panjang' => 70, 'harga' => 65000, 'rilis' => 35],
            ['bal' => $balSatu, 'kategori' => 'Dress', 'nama' => 'Dress Midi Bunga', 'merek' => 'Zara', 'grade' => 'B', 'lebar' => 46, 'panjang' => 110, 'harga' => 140000, 'rilis' => 35],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Sweater Rajut', 'merek' => 'Mango', 'grade' => 'Minus', 'lebar' => 55, 'panjang' => 64, 'harga' => 55000, 'rilis' => 35],
            ['bal' => $balDua, 'kategori' => 'Bawahan', 'nama' => 'Celana Chino', 'merek' => 'Dickies', 'grade' => 'A', 'lebar' => 44, 'panjang' => 102, 'harga' => 135000, 'rilis' => 7],
            ['bal' => $balDua, 'kategori' => 'Outerwear', 'nama' => 'Kardigan Rajut', 'merek' => 'Uniqlo', 'grade' => 'A', 'lebar' => 53, 'panjang' => 70, 'harga' => 155000, 'rilis' => 7],
            ['bal' => $balDua, 'kategori' => 'Dress', 'nama' => 'Dress Polkadot', 'merek' => 'Zara', 'grade' => 'B', 'lebar' => 45, 'panjang' => 108, 'harga' => 130000, 'rilis' => 6],
            ['bal' => $balDua, 'kategori' => 'Atasan', 'nama' => 'Blouse Sutra', 'merek' => 'H&M', 'grade' => 'A', 'lebar' => 49, 'panjang' => 62, 'harga' => 95000, 'rilis' => 6],
            ['bal' => $balDua, 'kategori' => 'Sepatu', 'nama' => 'Sneakers Retro', 'merek' => 'Nike', 'grade' => 'B', 'lebar' => 28, 'panjang' => 30, 'harga' => 220000, 'rilis' => 5],
            ['bal' => $balDua, 'kategori' => 'Aksesoris', 'nama' => 'Tas Kanvas', 'merek' => 'Vans', 'grade' => 'A', 'lebar' => 35, 'panjang' => 40, 'harga' => 110000, 'rilis' => 5],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Kemeja Oxford Putih', 'merek' => 'Uniqlo', 'grade' => 'A', 'lebar' => 51, 'panjang' => 70, 'harga' => 90000, 'rilis' => 34],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Kaos Graphic Band', 'merek' => 'H&M', 'grade' => 'B', 'lebar' => 50, 'panjang' => 68, 'harga' => 70000, 'rilis' => 33],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Blouse Pita Satin', 'merek' => 'Zara', 'grade' => 'A', 'lebar' => 48, 'panjang' => 60, 'harga' => 105000, 'rilis' => 31],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Crewneck Sweatshirt', 'merek' => 'Champion', 'grade' => 'B', 'lebar' => 56, 'panjang' => 66, 'harga' => 120000, 'rilis' => 29],
            ['bal' => $balSatu, 'kategori' => 'Atasan', 'nama' => 'Polo Stripe Knit', 'merek' => 'Lacoste', 'grade' => 'A', 'lebar' => 52, 'panjang' => 68, 'harga' => 135000, 'rilis' => 27],
            ['bal' => $balSatu, 'kategori' => 'Bawahan', 'nama' => 'Jeans Mom Fit', 'merek' => 'Wrangler', 'grade' => 'B', 'lebar' => 40, 'panjang' => 98, 'harga' => 140000, 'rilis' => 26],
            ['bal' => $balSatu, 'kategori' => 'Bawahan', 'nama' => 'Celana Kargo Utility', 'merek' => 'Dickies', 'grade' => 'A', 'lebar' => 45, 'panjang' => 104, 'harga' => 150000, 'rilis' => 24],
            ['bal' => $balSatu, 'kategori' => 'Bawahan', 'nama' => 'Rok Plisket Midi', 'merek' => 'Mango', 'grade' => 'A', 'lebar' => 36, 'panjang' => 80, 'harga' => 95000, 'rilis' => 23],
            ['bal' => $balSatu, 'kategori' => 'Bawahan', 'nama' => 'Shorts Denim Raw', 'merek' => 'Levis', 'grade' => 'B', 'lebar' => 42, 'panjang' => 52, 'harga' => 85000, 'rilis' => 21],
            ['bal' => $balSatu, 'kategori' => 'Bawahan', 'nama' => 'Track Pants Nylon', 'merek' => 'Adidas', 'grade' => 'A', 'lebar' => 44, 'panjang' => 100, 'harga' => 130000, 'rilis' => 19],
            ['bal' => $balDua, 'kategori' => 'Outerwear', 'nama' => 'Jaket Denim Trucker', 'merek' => 'Levis', 'grade' => 'B', 'lebar' => 53, 'panjang' => 64, 'harga' => 195000, 'rilis' => 17],
            ['bal' => $balDua, 'kategori' => 'Outerwear', 'nama' => 'Bomber Jacket Nylon', 'merek' => 'Alpha', 'grade' => 'A', 'lebar' => 55, 'panjang' => 66, 'harga' => 185000, 'rilis' => 16],
            ['bal' => $balDua, 'kategori' => 'Outerwear', 'nama' => 'Coat Wool Panjang', 'merek' => 'Zara', 'grade' => 'B', 'lebar' => 54, 'panjang' => 95, 'harga' => 240000, 'rilis' => 15],
            ['bal' => $balDua, 'kategori' => 'Outerwear', 'nama' => 'Cardigan Cable Knit', 'merek' => 'Gap', 'grade' => 'A', 'lebar' => 52, 'panjang' => 68, 'harga' => 145000, 'rilis' => 14],
            ['bal' => $balDua, 'kategori' => 'Outerwear', 'nama' => 'Vest Utility Pocket', 'merek' => 'Uniqlo', 'grade' => 'A', 'lebar' => 50, 'panjang' => 62, 'harga' => 115000, 'rilis' => 13],
            ['bal' => $balDua, 'kategori' => 'Dress', 'nama' => 'Dress Slip Satin', 'merek' => 'H&M', 'grade' => 'A', 'lebar' => 44, 'panjang' => 112, 'harga' => 160000, 'rilis' => 12],
            ['bal' => $balDua, 'kategori' => 'Dress', 'nama' => 'Dress Shirt Midi', 'merek' => 'Mango', 'grade' => 'B', 'lebar' => 46, 'panjang' => 105, 'harga' => 145000, 'rilis' => 11],
            ['bal' => $balDua, 'kategori' => 'Dress', 'nama' => 'Dress Knit Rib', 'merek' => 'Zara', 'grade' => 'A', 'lebar' => 42, 'panjang' => 98, 'harga' => 155000, 'rilis' => 10],
            ['bal' => $balDua, 'kategori' => 'Dress', 'nama' => 'Dress Maxi Floral', 'merek' => 'H&M', 'grade' => 'B', 'lebar' => 45, 'panjang' => 125, 'harga' => 175000, 'rilis' => 9],
            ['bal' => $balDua, 'kategori' => 'Dress', 'nama' => 'Dress Wrap Linen', 'merek' => 'Mango', 'grade' => 'A', 'lebar' => 47, 'panjang' => 110, 'harga' => 165000, 'rilis' => 8],
            ['bal' => $balDua, 'kategori' => 'Sepatu', 'nama' => 'Sneakers Canvas Low', 'merek' => 'Converse', 'grade' => 'B', 'lebar' => 27, 'panjang' => 29, 'harga' => 210000, 'rilis' => 7],
            ['bal' => $balDua, 'kategori' => 'Sepatu', 'nama' => 'Boots Chelsea Leather', 'merek' => 'Dr. Martens', 'grade' => 'A', 'lebar' => 28, 'panjang' => 30, 'harga' => 320000, 'rilis' => 6],
            ['bal' => $balDua, 'kategori' => 'Sepatu', 'nama' => 'Loafers Suede', 'merek' => 'Clarks', 'grade' => 'A', 'lebar' => 27, 'panjang' => 29, 'harga' => 260000, 'rilis' => 5],
            ['bal' => $balDua, 'kategori' => 'Sepatu', 'nama' => 'Sandal Kulit Slide', 'merek' => 'Birkenstock', 'grade' => 'B', 'lebar' => 26, 'panjang' => 28, 'harga' => 150000, 'rilis' => 4],
            ['bal' => $balDua, 'kategori' => 'Aksesoris', 'nama' => 'Shoulder Bag Nylon', 'merek' => 'Porter', 'grade' => 'A', 'lebar' => 30, 'panjang' => 24, 'harga' => 190000, 'rilis' => 6],
            ['bal' => $balDua, 'kategori' => 'Aksesoris', 'nama' => 'Topi Baseball Vintage', 'merek' => 'Nike', 'grade' => 'B', 'lebar' => 20, 'panjang' => 15, 'harga' => 75000, 'rilis' => 5],
            ['bal' => $balDua, 'kategori' => 'Aksesoris', 'nama' => 'Ikat Pinggang Kulit', 'merek' => 'Levis', 'grade' => 'A', 'lebar' => 90, 'panjang' => 4, 'harga' => 65000, 'rilis' => 4],
            ['bal' => $balDua, 'kategori' => 'Aksesoris', 'nama' => 'Syal Wool Kotak', 'merek' => 'Uniqlo', 'grade' => 'A', 'lebar' => 30, 'panjang' => 180, 'harga' => 85000, 'rilis' => 3],
        ];

        $produk = collect($produkData)->map(function (array $data) use ($kategori) {
            return Produk::create([
                'kode_sku' => Produk::generateSku($data['bal']->id_bal),
                'id_bal' => $data['bal']->id_bal,
                'id_kategori' => $kategori[$data['kategori']]->id_kategori,
                'nama_produk' => $data['nama'],
                'merek' => $data['merek'],
                'grade' => $data['grade'],
                'lebar_dada_cm' => $data['lebar'],
                'panjang_badan_cm' => $data['panjang'],
                'harga_jual' => $data['harga'],
                'stok' => 2 + (crc32($data['nama']) % 7),
                'status_produk' => Produk::STATUS_TERSEDIA,
                'tanggal_rilis' => now()->subDays($data['rilis'])->toDateString(),
            ]);
        });

        $terjual = $produk->take(3);
        $pesanan = Pesanan::create([
            'id_pengguna' => $pelanggan->id,
            'kanal' => 'website',
            'tanggal_pesanan' => now()->subDays(3),
            'batas_bayar' => now()->subDays(3)->addMinutes(15),
            'total_harga' => $terjual->sum(fn (Produk $p) => (float) $p->harga_jual),
            'ongkos_kirim' => 20000,
            'status_pesanan' => 'dibayar',
        ]);

        foreach ($terjual as $item) {
            DetailPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_produk' => $item->id_produk,
                'jumlah' => 1,
                'harga_satuan' => $item->harga_jual,
            ]);
            $item->update(['stok' => 0, 'status_produk' => Produk::STATUS_TERJUAL]);
        }

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode' => 'qris',
            'jumlah' => $pesanan->total_harga,
            'status_bayar' => 'berhasil',
            'waktu_bayar' => now()->subDays(3)->addMinutes(5),
            'ref_gateway' => 'SIM-'.strtoupper(uniqid()),
        ]);

        $pesananPos = Pesanan::create([
            'id_pengguna' => $owner->id,
            'kanal' => 'pos',
            'tanggal_pesanan' => now()->subDay(),
            'batas_bayar' => null,
            'total_harga' => $produk[6]->harga_jual,
            'ongkos_kirim' => 0,
            'status_pesanan' => 'dibayar',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesananPos->id_pesanan,
            'id_produk' => $produk[6]->id_produk,
            'jumlah' => 1,
            'harga_satuan' => $produk[6]->harga_jual,
        ]);
        $produk[6]->update(['stok' => 0, 'status_produk' => Produk::STATUS_TERJUAL]);

        Pembayaran::create([
            'id_pesanan' => $pesananPos->id_pesanan,
            'metode' => 'tunai',
            'jumlah' => $pesananPos->total_harga,
            'status_bayar' => 'berhasil',
            'waktu_bayar' => now()->subDay(),
            'ref_gateway' => 'POS-'.strtoupper(uniqid()),
        ]);

        $this->call(GambarProdukSeeder::class);
    }
}
