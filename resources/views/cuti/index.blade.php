@extends('layouts.global')
@section('title')
    Data Cuti Pegawai
@endsection

@section('content')
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="cabang" class="form-control">
                    <option value="">-- Semua Kantor --</option>
                    @foreach ($cabangs as $cab)
                        <option value="{{ $cab->id }}" {{ request('cabang') == $cab->id ? 'selected' : '' }}>
                            {{ $cab->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="text-center">
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Kantor</th>
                <th>Total Pengajuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cutis as $pegawaiId => $items)
                @php
                    $first = $items->first();
                    $pegawai = $first->pegawai ?? null;
                    $cabang = $first->cabang ?? null;
                @endphp

                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <b>{{ $pegawai->name ?? '-' }}</b>
                    </td>
                    <td>{{ $cabang->name ?? '-' }}</td>
                    <td>{{ $items->count() }}</td>
                    <td class="text-center">
                        @if ($pegawai)
                            <a href="{{ route('cuti.pegawai', [
                                'pegawai' => $pegawai->id,
                                'cabang' => request('cabang'),
                            ]) }}"
                                class="btn btn-info btn-sm">
                                Detail
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        Tidak ada data cuti
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
