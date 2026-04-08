<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Setelah verifikasi kode, user punya waktu 15 menit untuk set password baru.
     *
     * @var int
     */
    protected $verifiedSessionMinutes = 15;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request)
    {
        if (!$this->hasValidVerificationSession($request)) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Silakan lakukan verifikasi kode reset password terlebih dahulu.']);
        }

        $email = $request->session()->get('password_reset.email');

        return view('auth.passwords.reset', compact('email'));
    }

    public function reset(Request $request)
    {
        if (!$this->hasValidVerificationSession($request)) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Sesi reset password sudah berakhir. Silakan ulangi dari awal.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = $request->session()->get('password_reset.email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $request->session()->forget('password_reset');

            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $request->session()->forget('password_reset');

        return redirect()
            ->route('login')
            ->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    protected function hasValidVerificationSession(Request $request)
    {
        $verified = $request->session()->get('password_reset.verified');
        $email = $request->session()->get('password_reset.email');
        $verifiedAt = $request->session()->get('password_reset.verified_at');

        if (!$verified || !$email || !$verifiedAt) {
            return false;
        }

        $expiredAt = Carbon::parse($verifiedAt)->addMinutes($this->verifiedSessionMinutes);
        if (Carbon::now()->greaterThan($expiredAt)) {
            $request->session()->forget('password_reset');

            return false;
        }

        return true;
    }
}
