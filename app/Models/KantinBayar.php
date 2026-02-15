<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KantinBayar extends Model
{
    protected $table = 'tb_bayar';
    protected $primaryKey = 'id_bayar';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_bayar',
        'nominal_uang',
        'jumlah_bayar',
        'ppn',
        'nominal_toko',
        'nominal_rs',
        'diskon',
        'waktu_bayar',
        'kode_order_bayar',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(KantinOrder::class, 'id_bayar', 'id_order');
    }
}
