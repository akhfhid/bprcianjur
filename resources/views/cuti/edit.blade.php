@extends('layouts.global')

@section('title')
    Edit Cuti
@endsection

@section('content')
    <form method="POST" action="{{ route('cuti.update', $cuti->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Atasan 1 (Atasan Langsung)</label>
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
            <label>Status Atasan 1</label>
            <select name="statasan" class="form-control" required>
                <option value="SUBMIT" {{ $cuti->statasan == 'SUBMIT' ? 'selected' : '' }}>SUBMIT</option>
                <option value="DISETUJUI" {{ $cuti->statasan == 'DISETUJUI' ? 'selected' : '' }}>DISETUJUI</option>
                <option value="DITOLAK" {{ $cuti->statasan == 'DITOLAK' ? 'selected' : '' }}>DITOLAK</option>
            </select>
        </div>

        <div class="form-group">
            <label>Atasan 2 (Diketahui Atasan)</label>
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
            <label>Status Atasan 2</label>
            <select name="statdiket" class="form-control" required>
                <option value="SUBMIT" {{ $cuti->statdiket == 'SUBMIT' ? 'selected' : '' }}>SUBMIT</option>
                <option value="DISETUJUI" {{ $cuti->statdiket == 'DISETUJUI' ? 'selected' : '' }}>DISETUJUI</option>
                <option value="DITOLAK" {{ $cuti->statdiket == 'DITOLAK' ? 'selected' : '' }}>DITOLAK</option>
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
