@extends("layouts.global")
@section("title") Users List @endsection

@section("content")

<div class="row">
    <div class="col-md-6">
        <form action="{{ route('users.index') }}">
            <input
                value="{{ Request::get('keyword') }}"
                type="text"
                name="keyword"
                class="form-control"
                placeholder="Filter Berdasarkan Nama">
        </form>
    </div>
</div>

<br>

@if(session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<div class="row">
    <div class="col-md-12 text-right">
        {{-- Create hanya ADMIN --}}
        @can('ADMIN')
            <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
        @endcan
    </div>
</div>

<br>

<table class="table table-bordered">
    <thead>
        <tr class="text-center">
            <th>User ID</th>
            <th>Username</th>
            <th>Pegawai ID</th>
            <th>Email</th>
            <th>Avatar</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    @foreach($users as $user)
        <tr class="text-center">
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->pegawai_id }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" width="60">
                @else
                    N/A
                @endif
            </td>
            <td>
                <span class="badge {{ $user->status === 'ACTIVE' ? 'badge-success' : 'badge-secondary' }}">
                    {{ $user->status }}
                </span>
            </td>

            <td>
                {{-- ================= ADMIN ================= --}}
                @can('ADMIN')

                    <form
                        class="d-inline"
                        method="POST"
                        action="{{ route('users.destroy', $user->id) }}"
                        onsubmit="return confirm('Delete this user permanently?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>

                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-primary btn-sm">Detail</a>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm">Edit</a>

                    @if($user->status === 'INACTIVE')
                        <a href="{{ route('users.active', $user->id) }}"
                           class="btn btn-success btn-sm">
                            Aktivasi
                        </a>
                    @endif

                {{-- ================= ADMIN SDM ================= --}}
                @elseif(Gate::allows('ADMIN_SDM'))

                    <form method="POST" action="{{ route('users.update', $user->id) }}" class="d-inline">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="status"
                            value="{{ $user->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE' }}">

                        <button
                            class="btn btn-sm {{ $user->status === 'ACTIVE' ? 'btn-secondary' : 'btn-success' }}">
                            {{ $user->status === 'ACTIVE' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                {{-- ================= ROLE LAIN ================= --}}
                @else
                    <span class="text-muted">No Action</span>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="10">
                {{ $users->appends(Request::all())->links() }}
            </td>
        </tr>
    </tfoot>
</table>

@endsection
