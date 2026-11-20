<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasok_bal', function (Blueprint $table) {
            $table->id('id_bal');
            $table->string('kode_bal')->unique();
            $table->string('nama_pemasok');
            $table->date('tanggal_masuk');
            $table->decimal('harga_beli', 14, 2)->default(0);
            $table->decimal('berat_kg', 8, 2)->default(0);
            $table->unsignedInteger('jumlah_estimasi')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasok_bal');
    }
};
