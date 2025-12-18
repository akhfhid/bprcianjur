@extends('layouts.global')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Setup Otorisasi Cuti</h3> <br>
            @if ($cabangFilter)
                <div class="alert alert-info">
                    Daftar pegawai pada <strong>{{ $cabangs[$cabangFilter] ?? '' }}</strong>
                </div>
            @else
                <div class="alert alert-info">
                    Daftar pegawai pada <strong>Semua Kantor</strong>
                </div>
            @endif

        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('setuser.index') }}" class="row g-3">

                    <div class="col-md-4">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari nama..."
                            value="{{ request('keyword') }}">
                    </div>

                    <div class="col-md-4">
                        <select name="cabang" class="form-control">
                            <option value="">Semua Kantor</option>
                            @foreach ($cabangs as $id => $name)
                                <option value="{{ $id }}" @selected($cabangFilter == $id)>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex">
                        <button class="btn btn-primary me-2 flex-grow-1">Filter</button>

                        @if (request('keyword') || request('cabang'))
                            <a href="{{ route('setuser.index') }}" class="btn btn-secondary flex-grow-1">
                                Reset
                            </a>
                        @endif
                    </div>

                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr style="text-align:center; background:#f2f2f2;">
                            <th style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">No</th>
                            <th style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">Nama</th>
                            <th style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">Jabatan</th>
                            <th style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">Cabang</th>
                            <th style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">Atasan 1</th>
                            <th style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">Atasan 2</th>
                            <th width="130" style="padding:10px;font-weight:bold; border-bottom:2px solid #ccc;">Aksi</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse($pegawai as $p)
                            <tr>
                                <td>{{ $pegawai->firstItem() + $loop->index }}</td>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->relJabatan->name ?? '-' }}</td>
                                <td>{{ $p->relCabang->name ?? '-' }}</td>
                                <td>{{ $p->atasan1_data->name ?? '-' }}</td>
                                <td>{{ $p->atasan2_data->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('setuser.edit', $p->id) }}" class="btn btn-primary btn-sm w-100">
                                        Set User
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3">
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $pegawai->appends(request()->query())->links() }}
        </div>

    </div>
@endsection
