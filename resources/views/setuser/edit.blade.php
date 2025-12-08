@extends('layouts.global')

@section('content')
    <div class="container">
        <h3>Edit User: {{ $pegawai->name }}</h3>
        <form method="POST" action="{{ route('setuser.update', $pegawai->id) }}">
            @csrf

            <div class="form-group">
                <label>Jabatan</label>
                <select name="jabatan" class="form-control" required>
                    @foreach ($jabatan as $id => $name)
                        <option value="{{ $id }}" @if ($pegawai->jabatan == $id) selected @endif>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Cabang</label>
                <select name="cabang" class="form-control" required>
                    @foreach ($cabang as $id => $name)
                        <option value="{{ $id }}" @if ($pegawai->cabang == $id) selected @endif>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Atasan 1</label>
                <select name="atasan1" class="form-control">
                    <option value="">-- Pilih Atasan 1 --</option>
                    @foreach ($jabatan as $id => $name)
                        <option value="{{ $id }}" @if ($pegawai->atasan1 == $id) selected @endif>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Atasan 2</label>
                <select name="atasan2" class="form-control">
                    <option value="">-- Pilih Atasan 2 --</option>
                    @foreach ($jabatan as $id => $name)
                        <option value="{{ $id }}" @if ($pegawai->atasan2 == $id) selected @endif>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>


            <button class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>

        </form>

    </div>
@endsection
