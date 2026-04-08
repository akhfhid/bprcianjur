<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
     * Kode verifikasi hanya berlaku selama 10 menit.
     *
     * @var int
     */
    protected $codeExpireMinutes = 10;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Kirim kode verifikasi 4 digit ke email user.
     */
    public function sendResetCodeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $code = (string) random_int(1000, 9999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($code),
                'created_at' => Carbon::now(),
            ]
        );

        try {
            Mail::send('emails.password_reset_code', [
                'code' => $code,
                'expireMinutes' => $this->codeExpireMinutes,
            ], function ($message) use ($email) {
                $message->to($email)->subject('Kode Verifikasi Reset Password');
            });
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal mengirim kode verifikasi. Silakan coba lagi.']);
        }

        $request->session()->put('password_reset.email', $email);
        $request->session()->put('password_reset.verified', false);
        $request->session()->forget('password_reset.verified_at');

        return redirect()
            ->route('password.code.form')
            ->with('status', 'Kode verifikasi 4 digit sudah dikirim ke email Anda.');
    }

    /**
     * Tampilkan form input kode verifikasi.
     */
    public function showCodeForm(Request $request)
    {
        $email = $request->session()->get('password_reset.email');

        if (!$email) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.code', compact('email'));
    }

    /**
     * Verifikasi kode 4 digit sebelum user bisa set password baru.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
        ]);

        $resetData = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetData) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.']);
        }

        $expiredAt = Carbon::parse($resetData->created_at)->addMinutes($this->codeExpireMinutes);
        if (Carbon::now()->greaterThan($expiredAt)) {
            DB::table('password_resets')->where('email', $request->email)->delete();

            return back()->withErrors(['code' => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim ulang kode.']);
        }

        if (!Hash::check($request->code, $resetData->token)) {
            return back()->withErrors(['code' => 'Kode verifikasi salah.']);
        }

        DB::table('password_resets')->where('email', $request->email)->delete();

        $request->session()->put('password_reset.email', $request->email);
        $request->session()->put('password_reset.verified', true);
        $request->session()->put('password_reset.verified_at', Carbon::now()->toDateTimeString());

        return redirect()
            ->route('password.reset')
            ->with('status', 'Kode valid. Silakan buat password baru.');
    }
}
