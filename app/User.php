<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    public function pegawai(){
        return $this->belongsto('\App\Pegawai');
    }

    public function ordercuty(){
        return $this->hashone('\App\ordercuti');
    }
    public function orderatur(){
        return $this->hashone('\App\orderatur');
    }

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'menu_permissions'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'  => 'datetime',
        'menu_permissions'   => 'array',
    ];

    /**
     * Cek apakah user memiliki akses ke menu tertentu.
     * Jika menu_permissions null, dianggap pakai hak akses default (roles).
     *
     * @param  string  $menuKey
     * @return bool
     */
    public function hasMenuAccess(string $menuKey): bool
    {
        if (is_null($this->menu_permissions)) {
            return true; // null = pakai default roles
        }

        return in_array($menuKey, $this->menu_permissions);
    }

    /**
     * Cek apakah user menggunakan custom menu permissions (bukan default).
     *
     * @return bool
     */
    public function hasCustomMenuPermissions(): bool
    {
        return !is_null($this->menu_permissions);
    }
}
