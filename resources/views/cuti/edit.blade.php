@extends('layouts.global')

@section('title')
    Edit Cuti
@endsection

@section('content')
    <form method="POST" action="{{ route('cuti.update', $cuti->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Atasan Langsung</label>
            <select name="otoatasan" class="form-control" required>
                <option value="">-- Pilih Jabatan Atasan --</option>
                @foreach ($jabatanList as $jab)
                    <option value="{{ $jab->id }}" {{ $cuti->otoatasan == $jab->id ? 'selected' : '' }}>
                        {{ $jab->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Diketahui Atasan</label>
            <select name="diketatasan" class="form-control" required>
                <option value="">-- Pilih Jabatan Atasan --</option>
                @foreach ($jabatanList as $jab)
                    <option value="{{ $jab->id }}" {{ $cuti->diketatasan == $jab->id ? 'selected' : '' }}>
                        {{ $jab->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Tanggal Awal</label>
            <input type="date" name="tglawal" class="form-control" value="{{ $cuti->tglawal }}" required>
        </div>

        <div class="form-group">
            <label>Tanggal Akhir</label>
            <input type="date" name="tglakhir" class="form-control" value="{{ $cuti->tglakhir }}" required>
        </div>

        <div class="form-group">
            <label>Alasan</label>
            <textarea name="alasan" class="form-control" rows="3" required>{{ $cuti->alasan }}</textarea>
        </div>

        <button class="btn btn-primary">Simpan</button>

        <a href="{{ route('cuti.pegawai', [
            'pegawai' => $pegawaiId,
            'cabang' => request('cabang'),
        ]) }}"
            class="btn btn-secondary">
            Batal
        </a>
    </form>
@endsection
