<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KantinUser extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'level',
        'Kios',
    ];

    protected $hidden = [
        'password',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(KantinOrder::class, 'kasir', 'id');
    }
}
