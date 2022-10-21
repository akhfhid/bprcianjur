<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cabang extends Model
{
    use SoftDeletes;

    public function pegawai(){
    	return $this->hashone('\App\Pegawai');
    }
    public function ordercuti(){
    	return $this->hashone('\App\ordercuti');
    }
     public function orderatur(){
        return $this->hashone('\App\orderatur');
    }

}
