<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form reset password berbasis kode.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'email' => $request->email,
            'token' => $token,
        ]);
    }

    /**
     * Proses reset password menggunakan kode 4 digit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:4',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'code.required' => 'Kode reset wajib diisi.',
            'code.digits' => 'Kode reset harus 4 digit angka.',
        ]);

        $email = trim($request->email);
        $code = trim($request->code);

        if (!Schema::hasTable('password_resets')) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Tabel password_resets tidak ditemukan. Hubungi administrator sistem.']);
        }

        $row = DB::table('password_resets')
            ->where('email', $email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$row) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'Permintaan reset password tidak ditemukan untuk email ini.']);
        }

        $expiredAt = Carbon::parse($row->created_at)->addMinutes(3);
        if (Carbon::now()->greaterThan($expiredAt)) {
            DB::table('password_resets')->where('email', $email)->delete();

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['code' => 'Kode reset sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        if (!Hash::check($code, $row->token)) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['code' => 'Kode reset tidak valid.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        DB::table('password_resets')->where('email', $email)->delete();
        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login kembali.');
    }
}
