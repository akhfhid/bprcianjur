<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class peraturan extends Model
{
    use SoftDeletes;

    public function orderatur(){
    	return $this->hashone('\App\orderatur');
    }
}
