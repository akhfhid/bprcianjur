<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use PDF;
use App\User;
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

    public function pangkat()
    {
        return $this->belongsTo('App\Pangkat');
    }
    public function kawin()
    {
        return $this->belongsTo('\App\Kawin');
    }
    public function jenkel()
    {
        return $this->belongsTo('\App\Jenkel');
    }
    public function hubungan()
    {
        return $this->belongsTo('\App\Hubungan');
    }
    public function pendidikan()
    {
        return $this->belongsTo('\App\Pendidikan');
    }
    public function user()
    {
        return $this->hashMany('App\User');
    }

    public function keluarga()
    {
        return $this->hashMany('App\keluarga');
    }

    public function riwayatpendi()
    {
        return $this->BelongsTo('App\riwayatpendi');
    }
    public function riwayatkerja()
    {
        return $this->BelongsTo('App\riwayatkerja');
    }
    public function ordercuti()
    {
        return $this->hashone('App\ordercuti');
    }
    public function mutasi()
    {
        return $this->hashone('App\mutasi');
    }
    public function mutasipangkat()
    {
        return $this->hashone('App\mutasipangkat');
    }
    public function orderatur()
    {
        return $this->hashone('\App\orderatur');
    }
    public function berkala()
    {
        return $this->belongsto('\App\berkala');
    }
    public function gaji()
    {
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

    /**
     * Get effective Tunjangan Kinerja rate (decimal multiplier).
     * If custom percentage is enabled, returns custom_tuncab_val.
     * Otherwise resolves rate from Cabang table using tuncab ID.
     */
    public function getTuncabRateAttribute()
    {
        if (!empty($this->is_custom_tuncab)) {
            return (float) ($this->custom_tuncab_val ?? 0);
        }
        if ($this->tuncab) {
            $cabang = \App\Cabang::find($this->tuncab);
            return $cabang ? (float) $cabang->tunjangan : 0.0;
        }
        return 0.0;
    }

    /**
     * Get label for Tunjangan Kinerja setting.
     */
    public function getTunjanganKinerjaLabelAttribute()
    {
        if (!empty($this->is_custom_tuncab)) {
            $pct = (float)($this->custom_tuncab_val ?? 0) * 100;
            return "Custom (" . $pct . "%)";
        }
        if ($this->tuncab) {
            $cabang = \App\Cabang::find($this->tuncab);
            return $cabang ? $cabang->name : '-';
        }
        return '-';
    }
}

