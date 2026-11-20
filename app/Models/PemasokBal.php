<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PemasokBal extends Model
{
    protected $table = 'pemasok_bal';

    protected $primaryKey = 'id_bal';

    protected $fillable = [
        'kode_bal',
        'nama_pemasok',
        'tanggal_masuk',
        'harga_beli',
        'berat_kg',
        'jumlah_estimasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'harga_beli' => 'decimal:2',
            'berat_kg' => 'decimal:2',
        ];
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_bal', 'id_bal');
    }

    public function biaya(): HasMany
    {
        return $this->hasMany(BiayaBal::class, 'id_bal', 'id_bal');
    }

    public function totalBiaya(): float
    {
        return (float) $this->biaya()->sum('nominal');
    }
}
