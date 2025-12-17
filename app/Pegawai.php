<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use PDF;

class pegawai extends Model
{
    use softDeletes;
    public function relJabatan()
    {
        return $this->belongsTo(\App\Jabatan::class, 'jabatan', 'id');
    }
    
    public function relCabang()
    {
        return $this->belongsTo(\App\Cabang::class, 'cabang', 'id');
    }
    
    public function jabatan()
    {
        return $this->belongsTo(\App\Jabatan::class, 'jabatan', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(\App\Cabang::class, 'cabang', 'id');
    }

    public function pangkat(){
    	return $this->belongsTo('App\Pangkat');

    }
    public function kawin(){
    	return $this->belongsTo('\App\Kawin');
    }
    public function jenkel(){
    	return $this->belongsTo('\App\Jenkel');
    }
    public function hubungan(){
    	return $this->belongsTo('\App\Hubungan');
    }
    public function pendidikan(){
        return $this->belongsTo('\App\Pendidikan');
    }
    public function user(){
        return $this->hashMany('App\User');
    }

    public function keluarga(){
        return $this->hashMany('App\keluarga');
    }

    public function riwayatpendi(){
        return $this->BelongsTo('App\riwayatpendi');
    }
    public function riwayatkerja(){
        return $this->BelongsTo('App\riwayatkerja');
    }
    public function ordercuti(){
        return $this->hashone('App\ordercuti');
    }
    public function mutasi(){
        return $this->hashone('App\mutasi');
    }
    public function mutasipangkat(){
        return $this->hashone('App\mutasipangkat');
    }
     public function orderatur(){
        return $this->hashone('\App\orderatur');
    }
    public function berkala(){
        return $this->belongsto('\App\berkala');
    }
    public function gaji(){
        return $this->hashone('App\gaji');
    }
    public function relUser()
{
    return $this->hasOne(\App\User::class, 'pegawai_id', 'id');
}


   // public function AtributUmur(){
   // $now = \Carbon::now();
    //$bday= \Carbon::parse($tgllahir);
    //$umur= $bday->diffInYear($now);

    //$tglkerja = \Carbon::parse($tglmasuk);
    //$mkerja = $tglkerja->diffInYear($now);
    
      //  return \Carbon\Carbon::parse($this->attributes[$tgllahir])->umur;
   // }
    public function getAgeAttribute()
    {
    return Carbon::parse($this->attributes[$umur])->umur;
    }
}
