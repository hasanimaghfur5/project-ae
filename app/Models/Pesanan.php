<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_pengguna',
        'kanal',
        'tanggal_pesanan',
        'batas_bayar',
        'total_harga',
        'ongkos_kirim',
        'status_pesanan',
        'no_resi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pesanan' => 'datetime',
            'batas_bayar' => 'datetime',
            'total_harga' => 'decimal:2',
            'ongkos_kirim' => 'decimal:2',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna', 'id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaranBerhasil(): ?Pembayaran
    {
        return $this->pembayaran()->where('status_bayar', 'berhasil')->latest('id_pembayaran')->first();
    }

    public function isExpired(): bool
    {
        return $this->status_pesanan === 'pending'
            && $this->batas_bayar !== null
            && $this->batas_bayar->isPast();
    }
}
