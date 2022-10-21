<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
     public function pegawai(){
    	return $this->belongsToMany('\App\Pegawai');
    }
}
