<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantinKategoriMenu extends Model
{
    protected $table = 'tb_kategori_menu';
    protected $primaryKey = 'id_kategori';
    public $timestamps = false;

    protected $fillable = [
        'jenis_menu',
        'kategori_menu',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(KantinMenu::class, 'kategori', 'id_kategori');
    }
}
