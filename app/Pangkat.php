<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pangkat extends Model
{
    use SoftDeletes;
    public function pegawai(){
    	return $this->hashone("\App\Pegawai"); }
    public function berkala(){
        return $this->belongsto("\App\berkala");
    }
 }

