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
                    @foreach ($cabangs as $id => $name)
                        <option value="{{ $id }}" {{ request('cabang') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach

                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-sm">
        <thead class="text-center font-weight-bold">
            <tr>
                <th width="40">No</th>
                <th>Nama Pegawai</th>
                <th>Kantor</th>
                <th width="130">Total Pengajuan</th>
                <th width="90">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cutis as $pegawaiId => $items)
                @php
                    $pegawai = optional($items->first())->pegawai;
                    $cabang = optional($pegawai)->relCabang;
                @endphp

                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td>
                        <b>{{ $pegawai->name ?? '-' }}</b>
                    </td>

                    <td>
                        {{ $cabang->name ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $items->count() }}
                    </td>

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
                    <td colspan="5" class="text-center text-muted">
                        Tidak ada data cuti
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
@endsection
