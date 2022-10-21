<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mutasipangkat extends Model
{
    public function pegawai(){
    	return $this->belongsto('\App\Pegawai');
    }
}
