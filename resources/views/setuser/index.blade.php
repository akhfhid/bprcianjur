@extends('layouts.global')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Set User</h3>
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
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $id => $name)
                                <option value="{{ $id }}" @selected(request('cabang') == $id)>
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
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Cabang</th>
                            <th>Atasan 1</th>
                            <th>Atasan 2</th>
                            <th width="130">Aksi</th>
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
