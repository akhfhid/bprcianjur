<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hubungan extends Model
{
    public function pegawai(){
    	return $this->belongsToMany('\App\Pegawai');
    }
}
