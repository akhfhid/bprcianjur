@extends('layouts.global')
@section('title') Data Cuti @endsection

@section('content')

@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead class="text-center">
        <tr>
            <th>Username</th>
            <th>Nama Pegawai</th>
            <th>Cabang</th>
            <th>Jenis Cuti</th>
            <th>Periode</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cutis as $cuti)
        <tr class="text-center">
            <td>{{ $cuti->user->username ?? '-' }}</td>
            <td>{{ $cuti->pegawai->name ?? '-' }}</td>
            <td>{{ $cuti->cabang ?? '-' }}</td>
            <td>{{ $cuti->jeniscuti }}</td>
            <td>{{ $cuti->tglawal }} s/d {{ $cuti->tglakhir }}</td>
            <td>{{ $cuti->jmlcuti }} hari</td>
            <td>
                <span class="badge bg-info">{{ $cuti->status }}</span>
            </td>
            <td>
                <a href="{{ route('cuti.show', $cuti->id) }}" class="btn btn-primary btn-sm">Detail</a>
                <a href="{{ route('cuti.edit', $cuti->id) }}" class="btn btn-warning btn-sm">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $cutis->links() }}

@endsection
