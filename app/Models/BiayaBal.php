<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiayaBal extends Model
{
    protected $table = 'biaya_bal';

    protected $primaryKey = 'id_biaya';

    protected $fillable = ['id_bal', 'jenis_biaya', 'nominal', 'tanggal'];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function bal(): BelongsTo
    {
        return $this->belongsTo(PemasokBal::class, 'id_bal', 'id_bal');
    }
}
