@extends('layouts.global')
@section('title') Detail Cuti @endsection

@section('content')

<table class="table table-bordered">
    <tr><th>Username</th><td>{{ $cuti->user->username }}</td></tr>
    <tr><th>Nama Pegawai</th><td>{{ $cuti->pegawai->name }}</td></tr>
    <tr><th>Cabang</th><td>{{ $cuti->cabang->name }}</td></tr>
    <tr><th>Jenis Cuti</th><td>{{ $cuti->jeniscuti }}</td></tr>
    <tr><th>Periode</th><td>{{ $cuti->tglawal }} - {{ $cuti->tglakhir }}</td></tr>
    <tr><th>Jumlah</th><td>{{ $cuti->jmlcuti }} hari</td></tr>
    <tr><th>Alasan</th><td>{{ $cuti->alasan }}</td></tr>
    <tr><th>Status</th><td>{{ $cuti->status }}</td></tr>
</table>

<a href="{{ route('cuti.index') }}" class="btn btn-secondary">Kembali</a>

@endsection
