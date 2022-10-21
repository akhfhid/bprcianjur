<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class gaji extends Model
{
    public function Pegawai(){
        return $this->belongsToMany('App/Pegawai');
    }
}
