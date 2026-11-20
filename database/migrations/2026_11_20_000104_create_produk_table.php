<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('kode_sku')->unique();
            $table->foreignId('id_bal')->constrained('pemasok_bal', 'id_bal')->cascadeOnDelete();
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->cascadeOnDelete();
            $table->string('nama_produk');
            $table->string('merek')->nullable();
            $table->enum('grade', ['A', 'B', 'Minus']);
            $table->decimal('lebar_dada_cm', 6, 2);
            $table->decimal('panjang_badan_cm', 6, 2);
            $table->decimal('harga_jual', 14, 2);
            $table->enum('status_produk', ['tersedia', 'dipesan', 'terjual'])->default('tersedia');
            $table->date('tanggal_rilis')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index('kode_sku');
            $table->index('status_produk');
            $table->index('merek');
            $table->index('id_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
