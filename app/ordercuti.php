<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ordercuti extends Model
{
    protected $table = 'ordercutis';

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(\App\Pegawai::class, 'pegawai_id');
    }

    public function cabang()
    {
        return $this->belongsTo(\App\Cabang::class, 'cabang');
    }
}
