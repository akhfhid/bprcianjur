<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeraturanViewSession extends Model
{
    protected $table = 'peraturan_view_sessions';

    protected $fillable = [
        'user_id',
        'pegawai_id',
        'peraturan_id',
        'role',
        'page_url',
        'started_at',
        'last_seen_at',
        'ended_at',
        'active_seconds',
        'idle_seconds',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
