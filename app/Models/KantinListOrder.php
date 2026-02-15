<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KantinListOrder extends Model
{
    protected $table = 'tb_list_order';
    protected $primaryKey = 'id_list_order';
    public $timestamps = false;

    protected $fillable = [
        'menu',
        'kode_order',
        'jumlah',
        'catatan_order',
        'status',
    ];

    public function menuRel(): BelongsTo
    {
        return $this->belongsTo(KantinMenu::class, 'menu', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(KantinOrder::class, 'kode_order', 'id_order');
    }
}
