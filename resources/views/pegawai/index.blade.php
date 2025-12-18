@extends('layouts.global')
@section('title', 'List Pegawai')
<style>
    <style>.toggle-active {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
</style>
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('pegawai.index') }}" class="flex-grow-1 mr-2">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Filter by Nama Pegawai"
                    value="{{ Request::get('keyword') }}" name="keyword">

                <div class="input-group-append">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <div>
            <a href="{{ route('pegawai.create') }}" class="btn btn-primary">
                + Tambah Pegawai
            </a>
        </div>
    </div>

    <div>
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('pegawai.index') }}">Published</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="{{ route('pegawai.trash') }}">Berhenti/Tidak Aktif</a>
            </li>
        </ul>
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Status</th>
                        <th>Masa Kerja</th>
                        <th>Pangkat</th>
                        <th>Jabatan</th>
                        <th>Kantor</th>
                        <th>Status Peg</th>
                        <th width="130">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pegawai as $p)
                        <tr>
                            <td>{{ $p['id'] }}</td>
                            <td>{{ $p['name'] }}</td>
                            <td>{{ $p['nikpegawai'] }}</td>
                            <td>{{ $p['status'] }}</td>
                            <td>{{ $p['mkerja'] }} Tahun</td>
                            <td>{{ $p['pangkat'] }}</td>
                            <td>{{ $p['jabatan'] }}</td>
                            <td>{{ $p['cabang'] }}</td>
                            <td>
                                <form method="POST" action="{{ route('pegawai.toggle-active', $p['id']) }}">
                                    @csrf

                                    <button class="btn btn-sm {{ $p['status_active'] ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $p['status_active'] ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>


                            <td>
                                <a href="{{ route('pegawai.edit', $p['id']) }}" class="btn btn-warning btn-sm mb-1 w-100">
                                    Edit
                                </a>

                                <a href="{{ route('pegawai.show', $p['id']) }}" class="btn btn-primary btn-sm mb-1 w-100">
                                    Detail
                                </a>

                                {{-- <form method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')"
                                    action="{{ route('pegawai.destroy', $p['id']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm w-100">Hapus</button>
                                </form> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <div class="mt-3">
                {{ $datapegawai->appends(Request::all())->links() }}
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-active').forEach(el => {
                el.addEventListener('change', function() {
                    let id = this.dataset.id;

                    fetch('/pegawai/toggle-active/' + id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': `{{ csrf_token() }}`,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(console.log);
                });
            });
        });
    </script>
@endpush
