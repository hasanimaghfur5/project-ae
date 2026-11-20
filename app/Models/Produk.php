<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Produk extends Model
{
    public const STATUS_TERSEDIA = 'tersedia';

    public const STATUS_DIPESAN = 'dipesan';

    public const STATUS_TERJUAL = 'terjual';

    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'kode_sku',
        'id_bal',
        'id_kategori',
        'nama_produk',
        'merek',
        'grade',
        'lebar_dada_cm',
        'panjang_badan_cm',
        'harga_jual',
        'stok',
        'status_produk',
        'tanggal_rilis',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'lebar_dada_cm' => 'decimal:2',
            'panjang_badan_cm' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'stok' => 'integer',
            'tanggal_rilis' => 'date',
        ];
    }

    public function bal(): BelongsTo
    {
        return $this->belongsTo(PemasokBal::class, 'id_bal', 'id_bal');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }

    public function isTersedia(): bool
    {
        return $this->stok > 0;
    }

    /**
     * Membangkitkan kode SKU unik berformat TS-<KODEBAL>-<URUTAN>.
     * Satu potong pakaian = satu SKU (PA-01).
     */
    public static function generateSku(int $idBal): string
    {
        $bal = PemasokBal::findOrFail($idBal);
        $prefix = 'TS-'.Str::upper(Str::slug($bal->kode_bal, ''));

        $urutan = self::where('id_bal', $idBal)->count() + 1;

        do {
            $sku = $prefix.'-'.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
            $urutan++;
        } while (self::where('kode_sku', $sku)->exists());

        return $sku;
    }

    public static function labelStatus(): array
    {
        return [
            self::STATUS_TERSEDIA => 'Tersedia',
            self::STATUS_DIPESAN => 'Dipesan',
            self::STATUS_TERJUAL => 'Terjual',
        ];
    }
}
