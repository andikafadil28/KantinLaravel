<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantinOrder extends Model
{
    protected $table = 'tb_order';
    protected $primaryKey = 'id_order';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_order',
        'pelanggan',
        'meja',
        'kasir',
        'nama_kios',
        'waktu_order',
        'catatan',
    ];

    public function kasirUser(): BelongsTo
    {
        return $this->belongsTo(KantinUser::class, 'kasir', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KantinListOrder::class, 'kode_order', 'id_order');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(KantinBayar::class, 'id_bayar', 'id_order');
    }
}
