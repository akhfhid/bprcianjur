<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class riwayatangkat extends Model
{
    public function Pegawai(){
        return $this->hashmany('App/Pegawai');
    }
}