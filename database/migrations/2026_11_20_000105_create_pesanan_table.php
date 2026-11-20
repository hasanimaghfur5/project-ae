<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->foreignId('id_pengguna')->constrained('users')->cascadeOnDelete();
            $table->enum('kanal', ['website', 'pos'])->default('website');
            $table->timestamp('tanggal_pesanan');
            $table->timestamp('batas_bayar')->nullable();
            $table->decimal('total_harga', 14, 2)->default(0);
            $table->decimal('ongkos_kirim', 12, 2)->default(0);
            $table->enum('status_pesanan', ['pending', 'dibayar', 'selesai', 'batal'])->default('pending');
            $table->string('no_resi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
