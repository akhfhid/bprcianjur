@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verifikasi Kode') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-4">
                        Kode verifikasi 4 digit sudah dikirim ke email
                        <strong>{{ $email }}</strong>.
                    </p>

                    <form method="POST" action="{{ route('password.code.verify') }}">
                        @csrf

                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="form-group row">
                            <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Kode 4 Digit') }}</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required maxlength="4" inputmode="numeric" autocomplete="one-time-code" autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verifikasi Kode') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('password.request') }}">Kirim ulang kode</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
