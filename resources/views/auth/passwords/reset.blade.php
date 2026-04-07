@extends('layouts.auth-modern')

@section('title', 'Reset Password')

@section('content')
    <h1 class="auth-title">Atur Ulang Password</h1>
    <p class="auth-desc">Masukkan email, kode verifikasi 4 digit, dan password baru Anda.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label-modern">Email Akun</label>
            <input id="email" type="email" name="email"
                value="{{ old('email', $email ?? '') }}"
                class="form-control-modern @error('email') is-invalid @enderror"
                required autocomplete="email" autofocus>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="code" class="form-label-modern">Kode Verifikasi (4 Digit)</label>
            <input id="code" type="text" name="code" maxlength="4"
                value="{{ old('code') }}"
                class="form-control-modern @error('code') is-invalid @enderror"
                required autocomplete="one-time-code" placeholder="Contoh: 1234">
            @error('code')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label-modern">Password Baru</label>
            <input id="password" type="password" name="password"
                class="form-control-modern @error('password') is-invalid @enderror"
                required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm" class="form-label-modern">Konfirmasi Password Baru</label>
            <input id="password-confirm" type="password" name="password_confirmation"
                class="form-control-modern" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-auth btn-block mb-3">Simpan Password Baru</button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Kembali ke halaman login</a>
        </div>
    </form>
@endsection
