<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class riwayatpendi extends Model
{
    public function Pegawai(){
    	return $this->hashmany('App/Pegawai');
    }
}
