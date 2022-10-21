<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class berkala extends Model
{
    //use SoftDeletes;
    
    public function pegawai(){
        return $this->hashmany("\App\Pegawai");
    }
    public function pangkat(){
        return $this->hashmany("\App\Pangkat");
    }
}
