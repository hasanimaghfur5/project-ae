# ReWear Thrift — Sistem Manajemen Thrift Store

Implementasi program dari laporan **“Penerapan TOGAF ADM dalam Perancangan Sistem Manajemen Thrift Store (Toko Pakaian Bekas) Berbasis Website”**.

Aplikasi berbasis Laravel + SQLite ini mewujudkan blueprint arsitektur pada laporan ke dalam bentuk prototipe fungsional: inventaris per produk dengan stok multi-unit dan SKU unik, penguncian stok 15 menit, katalog daring dengan keranjang berjumlah, POS toko fisik, dan laporan profitabilitas per bal.

## Teknologi

- PHP 8.3+ / Laravel 13
- SQLite (zero-config, dapat diganti MySQL sesuai dokumen)
- Blade + Tailwind CSS (CDN)
- PHPUnit (feature test)

## Modul yang Diimplementasikan

| No | Modul | Ringkasan |
|----|-------|-----------|
| 1 | Autentikasi & Hak Akses | Login/register, role owner, admin, kasir, pelanggan |
| 2 | Manajemen Bal & Biaya | CRUD bal, pencatatan biaya laundry/operasional |
| 3 | Kurasi & Produk | Input produk, stok per item, generator SKU otomatis, label QR siap cetak |
| 4 | Katalog | Foto produk asli, filter kategori, merek, grade, ukuran lebar dada, pencarian, pagination tanpa reload halaman |
| 5 | Keranjang & Checkout | Jumlah per item, pemotongan stok, booking 15 menit, pembayaran QRIS simulasi |
| 6 | Point of Sale (POS) | Scan/input SKU, jumlah unit, validasi stok, nota cetak |
| 7 | Laporan & Analitik | Pendapatan periodik, margin/ROI per bal, aging stok >30 hari |

## Akun Demo

Semua akun memakai kata sandi: `password`

| Role | Email |
|------|-------|
| Owner | owner@thrift.test |
| Admin Gudang / Kurator | admin@thrift.test |
| Kasir | kasir@thrift.test |
| Pelanggan | pelanggan@thrift.test |

## Cara Menjalankan

```bash
cd thrift-store
composer install
cp .env.example .env            # jika .env belum ada
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Buka `http://127.0.0.1:8000`.

### Pelepasan Booking Otomatis

Pesanan yang melewati batas pembayaran 15 menit dilepas oleh command:

```bash
php artisan thrift:expire-bookings
```

Untuk menjalankan penjadwalan otomatis (setiap menit):

```bash
php artisan schedule:work
```

Atau daftarkan cron pada server produksi:

```
* * * * * cd /path/thrift-store && php artisan schedule:run >> /dev/null 2>&1
```

## Pengujian

```bash
php artisan test
```

Cakupan test: keunikan SKU, pemotongan stok sesuai jumlah keranjang, penolakan oversell, booking 15 menit, pengembalian stok saat kedaluwarsa, penjualan multi-unit di POS, perhitungan margin per bal, pembatasan role, dan smoke test seluruh halaman utama.

## Struktur Data (8 Entitas sesuai laporan)

`users`, `pemasok_bal`, `biaya_bal`, `kategori`, `produk`, `pesanan`, `detail_pesanan`, `pembayaran`.

Relasi utama:

- `pemasok_bal` 1—N `produk` dan 1—N `biaya_bal`
- `kategori` 1—N `produk`
- `users` 1—N `pesanan`
- `pesanan` 1—N `detail_pesanan` dan 1—N `pembayaran`
- `produk` 1—N `detail_pesanan`

Stok produk tersimpan di `produk.stok`; katalog hanya menampilkan stok > 0. Status produk: `tersedia → dipesan` saat seluruh stok terkunci, lalu `terjual` setelah dibayar. Booking yang kedaluwarsa mengembalikan stok sesuai jumlah di `detail_pesanan.jumlah`.

## Pemetaan Prinsip Arsitektur

| Prinsip | Implementasi |
|---------|--------------|
| PA-01 Single Source of Truth | Satu tabel `produk` dengan `kode_sku` unik dipakai katalog, checkout, dan POS |
| PA-02 Keterlacakan Modal per Bal | `produk.id_bal` + `biaya_bal`; laporan laba/ROI per bal |
| PA-03 Penguncian Inventaris | Status `dipesan` + `batas_bayar` 15 menit + command expire |
| PA-04 Web Responsif | Layout Blade responsif untuk ponsel, tablet, dan desktop |
| PA-05 Standar Terbuka | Laravel, SQLite/MySQL, HTML/CSS/JS standar |

## Catatan

- Pembayaran dan ongkir disimulasikan (tidak terhubung payment gateway nyata).
- SQLite dipakai agar mudah dijalankan. Untuk MySQL, ubah `DB_CONNECTION=mysql` dan kredensial pada `.env`; skema migration kompatibel.
