@extends('layouts.auth-modern')

@section('title', 'Login')

@section('content')
    <h1 class="auth-title">Masuk ke Akun</h1>
    <p class="auth-desc">Gunakan email dan password akun Anda untuk masuk ke aplikasi.</p>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" aria-label="Login">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label-modern">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-control-modern @error('email') is-invalid @enderror" required autofocus autocomplete="email"
                placeholder="contoh@bprcianjur.co.id">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label-modern">Password</label>
            <input id="password" type="password" name="password"
                class="form-control-modern @error('password') is-invalid @enderror" required autocomplete="current-password"
                placeholder="Masukkan password">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember" name="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="custom-control-label" for="remember">Ingat saya</label>
            </div>
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-auth btn-block">Masuk</button>
    </form>
@endsection
