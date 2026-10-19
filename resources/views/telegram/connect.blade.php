@extends('layouts.global')
@section('title')Koneksi Telegram @endsection
@section('content')

<div class="row">
    <div class="col-md-8">
        <h3><span class="oi oi-chat"></span> Koneksi Telegram</h3>
        <p class="text-muted">
            Hubungkan akun Telegram Anda untuk menerima notifikasi cuti, permintaan download peraturan,
            dan pemberitahuan peraturan baru.
        </p>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @include('partials.telegram_connect')
    </div>
</div>

@endsection
