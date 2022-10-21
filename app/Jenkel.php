<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jenkel extends Model
{
   public function pegawai(){
    	return $this->hashone('\App\Pegawai');
    }
}
