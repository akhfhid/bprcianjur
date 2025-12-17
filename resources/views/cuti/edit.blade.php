@extends('layouts.global')
@section('title') Edit Cuti @endsection

@section('content')

<form method="POST" action="{{ route('cuti.update', $cuti->id) }}">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Tanggal Awal</label>
        <input type="date" name="tglawal" class="form-control" value="{{ $cuti->tglawal }}">
    </div>

    <div class="form-group">
        <label>Tanggal Akhir</label>
        <input type="date" name="tglakhir" class="form-control" value="{{ $cuti->tglakhir }}">
    </div>

    <div class="form-group">
        <label>Alasan</label>
        <textarea name="alasan" class="form-control">{{ $cuti->alasan }}</textarea>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('cuti.index') }}" class="btn btn-secondary">Batal</a>
</form>

@endsection