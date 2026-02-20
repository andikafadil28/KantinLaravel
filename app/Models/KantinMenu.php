<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantinMenu extends Model
{
    protected $table = 'tb_menu';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'foto',
        'nama',
        'keterangan',
        'kategori',
        'nama_toko',
        'harga',
        'pajak',
        'status',
    ];

    public function kategoriRel(): BelongsTo
    {
        return $this->belongsTo(KantinKategoriMenu::class, 'kategori', 'id_kategori');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(KantinListOrder::class, 'menu', 'id');
    }
}
