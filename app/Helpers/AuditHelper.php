<?php

namespace App\Helpers;

use App\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditHelper
{
    public static function log(
        string $action,
        $target = null,
        array $oldData = null,
        array $newData = null
    ) {
        AuditLog::create([
            'actor_id'   => Auth::id(),
            'target_id'  => $target ? $target->id : null,
            'action'     => $action,
            'old_data'   => $oldData,
            'new_data'   => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
