<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_bal', function (Blueprint $table) {
            $table->id('id_biaya');
            $table->foreignId('id_bal')->constrained('pemasok_bal', 'id_bal')->cascadeOnDelete();
            $table->string('jenis_biaya');
            $table->decimal('nominal', 14, 2)->default(0);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_bal');
    }
};
