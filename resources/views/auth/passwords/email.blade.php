@extends('layouts.auth-modern')

@section('title', 'Lupa Password')

@section('content')
    <h1 class="auth-title">Lupa Password</h1>
    <p class="auth-desc">
        Masukkan email akun Anda. Kami akan kirim kode verifikasi 4 digit ke Gmail Anda.
    </p>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label-modern">Email Akun</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control-modern @error('email') is-invalid @enderror" required autofocus autocomplete="email"
                placeholder="contoh@bprcianjur.co.id">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-auth btn-block mb-3">Kirim Kode Verifikasi</button>

        @if(session('reset_email') || old('email'))
            <a href="{{ route('password.code.form', ['email' => session('reset_email', old('email'))]) }}"
               class="btn btn-outline-secondary btn-block mb-3" style="border-radius:12px;font-weight:700;">
                Saya Sudah Menerima Kode
            </a>
        @endif

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Kembali ke halaman login</a>
        </div>
    </form>
@endsection
