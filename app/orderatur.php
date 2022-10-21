<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class orderatur extends Model
{
     public function user(){
    	return $this->belongsto('\App\User');
    }
    public function pegawai(){
    	return $this->belongsto('\App\Pegawai');
    }
    public function cabang(){
    	return $this->belongsto('\App\Cabang');
    }
    public function peraturan(){
    	return $this->belongsto('\App\peraturan');
    }
}
