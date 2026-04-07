<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Notifications\ResetPasswordCodeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * Kirim kode reset password 4 digit (berlaku 3 menit) ke email user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak ditemukan pada sistem.',
        ]);

        $email = trim($request->email);
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan pada sistem.',
            ]);
        }

        if (!Schema::hasTable('password_resets')) {
            return back()->withErrors([
                'email' => 'Tabel password_resets tidak ditemukan. Hubungi administrator sistem.',
            ]);
        }

        $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $now = Carbon::now();

        DB::table('password_resets')->where('email', $email)->delete();
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => bcrypt($code),
            'created_at' => $now,
        ]);

        $user->notify(new ResetPasswordCodeNotification(
            $code,
            3,
            'Sistem Kepegawaian dan Peraturan',
            'BPR CIANJUR JABAR'
        ));

        return back()
            ->with('status', 'Kode reset password sudah dikirim ke email Anda.')
            ->with('reset_email', $email);
    }
}
