<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KantinKios extends Model
{
    protected $table = 'tb_kios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
    ];
}
