@extends('layouts.global')
@section('title')
    Detail Cuti Pegawai
@endsection

@section('content')
    <h6 class="mb-2">
        Riwayat Cuti: <b>{{ $pegawai->name }}</b>
    </h6>

    {{-- NOTIF --}}
    @if (session('status'))
        <div class="alert alert-success py-1 small">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-2 d-flex justify-content-between align-items-center">
        <a href="{{ route('cuti.index', ['cabang' => request('cabang')]) }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>

        <form method="GET" class="form-inline">
            <input type="hidden" name="cabang" value="{{ request('cabang') }}">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-1"
                style="width:220px" placeholder="Cari jenis / status / alasan">
            <button class="btn btn-sm btn-primary">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm text-nowrap small">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Jenis</th>
                    <th>Awal</th>
                    <th>Akhir</th>
                    <th>Hari</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Atasan 1</th>
                    <th>Stat A1</th>
                    <th>Atasan 2</th>
                    <th>Stat A2</th>
                    {{-- <th>SDM</th> --}}
                    {{-- <th>Stat SDM</th> --}}
                    <th width="130">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cutis as $cuti)
                    <tr class="text-center">
                        <td>{{ ($cutis->currentPage() - 1) * $cutis->perPage() + $loop->iteration }}</td>
                        <td class="text-left">{{ $cuti->jeniscuti }}</td>
                        <td>{{ $cuti->tglawal }}</td>
                        <td>{{ $cuti->tglakhir }}</td>
                        <td>{{ $cuti->jmlcuti }}</td>
                        <td class="text-left">{{ $cuti->alasan }}</td>

                        @php
                            $badge = [
                                'SUBMIT' => 'warning',
                                'DISETUJUI' => 'success',
                                'DITOLAK' => 'danger',
                            ];
                        @endphp
                        <td>
                            <span class="badge badge-{{ $badge[$cuti->status] ?? 'secondary' }}">
                                {{ $cuti->status }}
                            </span>
                        </td>

                        <td>{{ $cuti->otoatasan ?? '-' }}</td>
                        <td>{{ $cuti->statasan ?? '-' }}</td>
                        <td>{{ $cuti->diketatasan ?? '-' }}</td>
                        <td>{{ $cuti->statdiket ?? '-' }}</td>
                        {{-- <td>{{ $cuti->otosdm ?? '-' }}</td>
            <td>{{ $cuti->statsdm ?? '-' }}</td> --}}

                        <td>
                            <a href="{{ route('cuti.edit', $cuti->id) }}" class="btn btn-warning btn-sm px-1">
                                <span class="oi oi-pencil"></span>
                            </a>

                            <form action="{{ route('cuti.destroy', $cuti->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin hapus data cuti?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
                                <button class="btn btn-danger btn-sm px-1">
                                    <span class="oi oi-trash"></span>

                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center text-muted">
                            Data cuti tidak ditemukan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $cutis->links() }}
    </div>
@endsection
