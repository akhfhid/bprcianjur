<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class keluarga extends Model
{
    use SoftDeletes;

    public function pegawai(){
    	return $this->belongsto('App\Pegawai');
    }
}
