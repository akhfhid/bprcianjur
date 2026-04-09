<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'actor_id',
        'category',
        'channel',
        'status',
        'reference_type',
        'reference_id',
        'cabang_id',
        'recipient_pegawai_id',
        'recipient_name',
        'recipient_email',
        'recipient_phone',
        'subject',
        'message',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}

