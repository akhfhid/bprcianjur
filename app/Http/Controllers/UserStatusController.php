<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Gate;
use App\Helpers\AuditHelper;

class UserStatusController extends Controller
{
    public function deactivate(User $user)
    {
        if (!Gate::allows('ADMIN_SDM') && !Gate::allows('ADMIN')) {
            abort(403);
        }

        $old = $user->only(['status']);

        $user->status = 'INACTIVE';
        $user->save();

        AuditHelper::log(
            'deactivate_user',
            $user,
            $old,
            $user->only(['status'])
        );

        return back()->with('status', 'User berhasil dinonaktifkan');
    }

    public function activate(User $user)
    {
        if (!Gate::allows('ADMIN_SDM') && !Gate::allows('ADMIN')) {
            abort(403);
        }

        $old = $user->only(['status']);

        $user->status = 'ACTIVE';
        $user->save();

        AuditHelper::log(
            'activate_user',
            $user,
            $old,
            $user->only(['status'])
        );

        return back()->with('status', 'User berhasil diaktifkan');
    }
}
