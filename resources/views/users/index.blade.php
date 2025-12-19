@extends('layouts.global')
@section('title')
    Users List
@endsection

@section('content')

    {{-- Header & Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800">Manajemen User</h4>
        @can('ADMIN')
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm"></i> Create New User
            </a>
        @endcan
    </div>

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row gx-3 gy-2 align-items-center">
                <div class="col-sm-5">
                    <label class="sr-only">Nama</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><i class="fas fa-search"></i></div>
                        </div>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                            placeholder="Cari nama atau username...">
                    </div>
                </div>
                <div class="col-sm-4">
                    <select name="status" class="form-control">
                        <option value="">-- Semua Status --</option>
                        <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Aktif</option>
                        <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-info btn-block text-white">
                        Filter Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr class="text-center text-uppercase">
                            <th width="5%">User ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Pegawai ID</th>
                            <th>Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="text-center align-middle">{{ $user->id }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            @if ($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                                    class="rounded-circle shadow-sm" width="40" height="40"
                                                    style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                                    style="width: 40px; height: 40px;">
                                                    <small>{{ substr($user->username, 0, 2) }}</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $user->username }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">{{ $user->email }}</td>
                                <td class="align-middle text-center">{{ $user->pegawai_id }}</td>
                                <td class="align-middle text-center">
                                    @if ($user->status === 'ACTIVE')
                                        <span class="badge badge-pill badge-success px-3">ACTIVE</span>
                                    @else
                                        <span class="badge badge-pill badge-danger px-3">INACTIVE</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group" role="group">
                                        {{-- Action untuk ADMIN --}}
                                        @can('ADMIN')
                                            <a href="{{ route('users.show', $user->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail"><i
                                                    class="fas fa-eye"></i></a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-info"
                                                title="Edit"><i class="fas fa-edit"></i></a>

                                            <form class="d-inline" method="POST"
                                                action="{{ route('users.destroy', $user->id) }}"
                                                onsubmit="return confirm('Hapus user ini secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        @endcan

                                        {{-- Action khusus ADMIN SDM / Toggle Status --}}
                                        @if (Gate::allows('ADMIN_SDM') || Gate::allows('ADMIN'))
                                            @if ($user->status === 'ACTIVE')
                                                <form method="POST" action="{{ route('users.deactivate', $user->id) }}"
                                                    class="d-inline ml-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-sm btn-warning shadow-sm">
                                                        Nonaktifkan
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('users.activate', $user->id) }}"
                                                    class="d-inline ml-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="btn btn-sm btn-success shadow-sm">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->appends(Request::all())->links() }}
            </div>
        </div>
    </div>

@endsection
