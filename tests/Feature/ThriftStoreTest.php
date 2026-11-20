<?php

namespace Tests\Feature;

use App\Models\BiayaBal;
use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\PemasokBal;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThriftStoreTest extends TestCase
{
    use RefreshDatabase;

    private function bal(array $overrides = []): PemasokBal
    {
        return PemasokBal::create(array_merge([
            'kode_bal' => 'B001',
            'nama_pemasok' => 'Pemasok Uji',
            'tanggal_masuk' => now()->subDays(5)->toDateString(),
            'harga_beli' => 100000,
            'berat_kg' => 10,
            'jumlah_estimasi' => 20,
        ], $overrides));
    }

    private function produk(array $overrides = []): Produk
    {
        $bal = $overrides['bal'] ?? $this->bal();
        unset($overrides['bal']);
        $kategori = Kategori::firstOrCreate(['nama_kategori' => 'Atasan']);

        return Produk::create(array_merge([
            'kode_sku' => Produk::generateSku($bal->id_bal),
            'id_bal' => $bal->id_bal,
            'id_kategori' => $kategori->id_kategori,
            'nama_produk' => 'Kemeja Uji',
            'merek' => 'Uniqlo',
            'grade' => 'A',
            'lebar_dada_cm' => 50,
            'panjang_badan_cm' => 70,
            'harga_jual' => 200000,
            'stok' => 5,
            'status_produk' => Produk::STATUS_TERSEDIA,
            'tanggal_rilis' => now()->toDateString(),
        ], $overrides));
    }

    private function user(string $peran = 'pelanggan'): User
    {
        return User::factory()->create(['peran' => $peran]);
    }

    public function test_sku_dibangkitkan_unik_per_bal(): void
    {
        $bal = $this->bal();
        $satu = $this->produk(['bal' => $bal]);
        $dua = $this->produk(['bal' => $bal]);

        $this->assertNotSame($satu->kode_sku, $dua->kode_sku);
        $this->assertStringStartsWith('TS-B001-', $satu->kode_sku);
        $this->assertSame('TS-B001-0001', $satu->kode_sku);
        $this->assertSame('TS-B001-0002', $dua->kode_sku);
    }

    public function test_checkout_mengunci_stok_15_menit_lalu_pembayaran_menjadi_terjual(): void
    {
        $user = $this->user();
        $produk = $this->produk(['stok' => 1]);

        $this->actingAs($user)
            ->withSession(['cart' => [$produk->id_produk => 1]])
            ->post(route('checkout.store'))
            ->assertRedirect();

        $produk->refresh();
        $this->assertSame(0, $produk->stok);
        $this->assertSame(Produk::STATUS_DIPESAN, $produk->status_produk);

        $pesanan = Pesanan::firstOrFail();
        $this->assertSame(1, $pesanan->detail()->first()->jumlah);
        $this->assertSame('pending', $pesanan->status_pesanan);
        $this->assertNotNull($pesanan->batas_bayar);
        $this->assertTrue(
            $pesanan->batas_bayar->between(now()->addMinutes(14), now()->addMinutes(16)),
            'Batas bayar harus sekitar 15 menit dari sekarang.'
        );

        $this->actingAs($user)
            ->post(route('pesanan.pay', $pesanan))
            ->assertRedirect();

        $this->assertSame(Produk::STATUS_TERJUAL, $produk->fresh()->status_produk);
        $this->assertSame('dibayar', $pesanan->fresh()->status_pesanan);
        $this->assertSame('berhasil', $pesanan->pembayaran()->first()->status_bayar);
    }

    public function test_pembeli_kedua_tidak_bisa_mengunci_item_yang_sama(): void
    {
        $produk = $this->produk(['stok' => 1]);
        $pembeliA = $this->user();
        $pembeliB = $this->user();

        $this->actingAs($pembeliA)
            ->withSession(['cart' => [$produk->id_produk => 1]])
            ->post(route('checkout.store'))
            ->assertRedirect();

        $this->actingAs($pembeliB)
            ->withSession(['cart' => [$produk->id_produk => 1]])
            ->post(route('checkout.store'))
            ->assertSessionHas('error');

        $this->assertSame(0, $produk->fresh()->stok);
        $this->assertSame(Produk::STATUS_DIPESAN, $produk->fresh()->status_produk);
        $this->assertSame(1, Pesanan::count());
    }

    public function test_pos_menolak_item_booking_dan_menjual_item_tersedia(): void
    {
        $kasir = $this->user('kasir');
        $produk = $this->produk(['stok' => 1]);

        $produk->update(['stok' => 0, 'status_produk' => Produk::STATUS_DIPESAN]);

        $this->actingAs($kasir)
            ->post(route('pos.store'), ['kode_sku' => $produk->kode_sku, 'metode' => 'tunai', 'jumlah' => 1])
            ->assertSessionHas('error');

        $this->assertSame(Produk::STATUS_DIPESAN, $produk->fresh()->status_produk);

        $produk->update(['stok' => 1, 'status_produk' => Produk::STATUS_TERSEDIA]);

        $this->actingAs($kasir)
            ->post(route('pos.store'), ['kode_sku' => $produk->kode_sku, 'metode' => 'tunai', 'jumlah' => 1])
            ->assertRedirect();

        $this->assertSame(0, $produk->fresh()->stok);
        $this->assertSame(Produk::STATUS_TERJUAL, $produk->fresh()->status_produk);
        $this->assertSame(1, Pesanan::where('kanal', 'pos')->count());
        $this->assertSame('berhasil', Pembayaran::first()->status_bayar);
    }

    public function test_command_expire_melepas_booking_kedaluwarsa(): void
    {
        $user = $this->user();
        $produk = $this->produk(['stok' => 0, 'status_produk' => Produk::STATUS_DIPESAN]);

        $pesanan = Pesanan::create([
            'id_pengguna' => $user->id,
            'kanal' => 'website',
            'tanggal_pesanan' => now()->subMinutes(20),
            'batas_bayar' => now()->subMinutes(5),
            'total_harga' => 200000,
            'ongkos_kirim' => 20000,
            'status_pesanan' => 'pending',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $produk->id_produk,
            'harga_satuan' => 200000,
            'jumlah' => 1,
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode' => 'qris',
            'jumlah' => 220000,
            'status_bayar' => 'pending',
        ]);

        $this->artisan('thrift:expire-bookings')->assertSuccessful();

        $this->assertSame(1, $produk->fresh()->stok);
        $this->assertSame(Produk::STATUS_TERSEDIA, $produk->fresh()->status_produk);
        $this->assertSame('batal', $pesanan->fresh()->status_pesanan);
        $this->assertSame('gagal', Pembayaran::first()->status_bayar);
    }

    public function test_checkout_mengurangi_stok_sesuai_jumlah(): void
    {
        $user = $this->user();
        $produk = $this->produk(['stok' => 5]);

        $this->actingAs($user)
            ->withSession(['cart' => [$produk->id_produk => 3]])
            ->post(route('checkout.store'))
            ->assertRedirect();

        $this->assertSame(2, $produk->fresh()->stok);
        $this->assertSame(Produk::STATUS_TERSEDIA, $produk->fresh()->status_produk);

        $pesanan = Pesanan::firstOrFail();
        $this->assertSame(3, $pesanan->detail()->first()->jumlah);
        $this->assertSame(620000.0, (float) $pesanan->total_harga);
    }

    public function test_checkout_menolak_jumlah_melebihi_stok(): void
    {
        $user = $this->user();
        $produk = $this->produk(['stok' => 2]);

        $this->actingAs($user)
            ->withSession(['cart' => [$produk->id_produk => 3]])
            ->post(route('checkout.store'))
            ->assertSessionHas('error');

        $this->assertSame(2, $produk->fresh()->stok);
        $this->assertSame(0, Pesanan::count());
    }

    public function test_keranjang_membatasi_jumlah_sesuai_stok(): void
    {
        $user = $this->user();
        $produk = $this->produk(['stok' => 2]);

        $this->actingAs($user)->post(route('cart.add', $produk));
        $this->actingAs($user)->post(route('cart.add', $produk));

        $this->assertSame([$produk->id_produk => 2], session('cart'));

        $this->actingAs($user)->post(route('cart.add', $produk))->assertSessionHas('error');
        $this->assertSame([$produk->id_produk => 2], session('cart'));

        $this->actingAs($user)->patch(route('cart.update', $produk), ['jumlah' => 1])->assertRedirect();
        $this->assertSame([$produk->id_produk => 1], session('cart'));
    }

    public function test_command_expire_mengembalikan_stok_sesuai_jumlah(): void
    {
        $user = $this->user();
        $produk = $this->produk(['stok' => 0, 'status_produk' => Produk::STATUS_DIPESAN]);

        $pesanan = Pesanan::create([
            'id_pengguna' => $user->id,
            'kanal' => 'website',
            'tanggal_pesanan' => now()->subMinutes(20),
            'batas_bayar' => now()->subMinutes(5),
            'total_harga' => 620000,
            'ongkos_kirim' => 20000,
            'status_pesanan' => 'pending',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $produk->id_produk,
            'harga_satuan' => 200000,
            'jumlah' => 3,
        ]);

        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'metode' => 'qris',
            'jumlah' => 620000,
            'status_bayar' => 'pending',
        ]);

        $this->artisan('thrift:expire-bookings')->assertSuccessful();

        $this->assertSame(3, $produk->fresh()->stok);
        $this->assertSame(Produk::STATUS_TERSEDIA, $produk->fresh()->status_produk);
    }

    public function test_pos_menjual_beberapa_unit_dan_mengurangi_stok(): void
    {
        $kasir = $this->user('kasir');
        $produk = $this->produk(['stok' => 3]);

        $this->actingAs($kasir)
            ->post(route('pos.store'), ['kode_sku' => $produk->kode_sku, 'metode' => 'tunai', 'jumlah' => 2])
            ->assertRedirect();

        $this->assertSame(1, $produk->fresh()->stok);
        $this->assertSame(Produk::STATUS_TERSEDIA, $produk->fresh()->status_produk);

        $pesanan = Pesanan::where('kanal', 'pos')->firstOrFail();
        $this->assertSame(2, $pesanan->detail()->first()->jumlah);
        $this->assertSame(400000.0, (float) $pesanan->total_harga);
    }

    public function test_pos_menolak_jumlah_melebihi_stok(): void
    {
        $kasir = $this->user('kasir');
        $produk = $this->produk(['stok' => 1]);

        $this->actingAs($kasir)
            ->post(route('pos.store'), ['kode_sku' => $produk->kode_sku, 'metode' => 'tunai', 'jumlah' => 2])
            ->assertSessionHas('error');

        $this->assertSame(1, $produk->fresh()->stok);
        $this->assertSame(0, Pesanan::where('kanal', 'pos')->count());
    }

    public function test_laporan_menghitung_margin_bersih_per_bal(): void
    {
        $owner = $this->user('owner');
        $bal = $this->bal(['harga_beli' => 100000]);
        $produk = $this->produk(['bal' => $bal, 'harga_jual' => 200000, 'stok' => 0, 'status_produk' => Produk::STATUS_TERJUAL]);

        BiayaBal::create([
            'id_bal' => $bal->id_bal,
            'jenis_biaya' => 'Laundry',
            'nominal' => 20000,
            'tanggal' => now()->toDateString(),
        ]);

        $pesanan = Pesanan::create([
            'id_pengguna' => $owner->id,
            'kanal' => 'website',
            'tanggal_pesanan' => now(),
            'batas_bayar' => now()->addMinutes(15),
            'total_harga' => 200000,
            'ongkos_kirim' => 0,
            'status_pesanan' => 'dibayar',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $produk->id_produk,
            'harga_satuan' => 200000,
            'jumlah' => 1,
        ]);

        $this->actingAs($owner)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertViewHas('kinerjaBal', function ($rows) {
                $row = $rows->first();

                return abs($row->laba_bersih - 80000) < 0.01
                    && abs($row->pendapatan - 200000) < 0.01;
            });
    }

    public function test_pelanggan_tidak_bisa_mengakses_dashboard(): void
    {
        $this->actingAs($this->user())
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_menampilkan_grafik_operasional(): void
    {
        $this->seed();

        $owner = User::where('email', 'owner@thrift.test')->firstOrFail();

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('trenPendapatan', function ($tren) {
                return $tren->count() === 7 && $tren->sum('total') > 0;
            })
            ->assertViewHas('nilaiKategori', fn ($rows) => $rows->isNotEmpty())
            ->assertViewHas('produkTerlaris', fn ($rows) => $rows->isNotEmpty())
            ->assertSee('Pendapatan 7 Hari')
            ->assertSee('Nilai Inventaris per Kategori');
    }

    public function test_pagination_katalog_mengembalikan_partial_saat_ajax(): void
    {
        $this->seed();

        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('katalog.index', ['page' => 2]))
            ->assertOk()
            ->assertViewIs('katalog._koleksi')
            ->assertDontSee('<!DOCTYPE html>', false);
    }

    public function test_pagination_katalog_tanpa_ajax_tetap_memuat_halaman_penuh(): void
    {
        $this->seed();

        $this->get(route('katalog.index', ['page' => 2]))
            ->assertOk()
            ->assertViewIs('katalog.index')
            ->assertSee('<!DOCTYPE html>', false);
    }

    public function test_halaman_utama_dapat_dibuka_dengan_data_seed(): void
    {
        $this->seed();

        $owner = User::where('email', 'owner@thrift.test')->firstOrFail();
        $pelanggan = User::where('email', 'pelanggan@thrift.test')->firstOrFail();
        $bal = PemasokBal::firstOrFail();
        $produk = Produk::where('status_produk', Produk::STATUS_TERSEDIA)->firstOrFail();

        $this->get(route('katalog.index'))->assertOk();
        $this->get(route('katalog.show', $produk))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();

        foreach ([
            route('dashboard'),
            route('bal.index'),
            route('bal.show', $bal),
            route('bal.create'),
            route('produk.index'),
            route('produk.create'),
            route('produk.show', $produk),
            route('produk.label', $produk),
            route('pesanan.index'),
            route('laporan.index'),
            route('pos.index'),
        ] as $url) {
            $this->actingAs($owner)->get($url)->assertOk();
        }

        $this->actingAs($pelanggan)->get(route('cart.index'))->assertOk();
    }
}
