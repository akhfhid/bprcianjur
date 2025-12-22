<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ordercuti extends Model
{
    protected $table = 'ordercutis';
    protected $guarded = ['id'];
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
    public function atasan1()
    {
        return $this->belongsTo(\App\Pegawai::class, 'otoatasan', 'id');
    }

    public function atasan2()
    {
        return $this->belongsTo(\App\Pegawai::class, 'diketatasan', 'id');
    }
}
