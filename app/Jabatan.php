<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use SoftDeletes;
    
    public function pegawai(){
    	return $this->hashone("\App\Pegawai");
    }
        public function atasanJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'atasan');
    }
}
