<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->cascadeOnDelete();
            $table->enum('metode', ['transfer', 'qris', 'tunai'])->default('qris');
            $table->decimal('jumlah', 14, 2);
            $table->enum('status_bayar', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->timestamp('waktu_bayar')->nullable();
            $table->string('ref_gateway')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
