<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kawin extends Model
{
    public function pegawai(){
    	return $this->belongsToMany('\App\Pegawai');
    }
}
