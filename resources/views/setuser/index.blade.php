@extends('layouts.global')

@section('title')
    Setup Otorisasi Cuti
@endsection

@section('content')
<style>
    /* UI Custom Styles agar konsisten dengan modul lainnya */
    .page-header { background: #fff; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #edf2f7; }
    .filter-section { background: #f8fafc; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
    
    .table-container { background: #fff; border-radius: 15px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.07); overflow: hidden; }
    .table thead th { background: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; border: none; padding: 15px; }
    .table td { padding: 12px 15px; vertical-align: middle !important; }
    
    .btn-rounded { border-radius: 50px; padding-left: 1.2rem; padding-right: 1.2rem; }
    .form-control-custom { border-radius: 8px; border: 1px solid #e2e8f0; }
    
    .badge-atasan { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; border: 1px solid #e2e8f0; }
    .badge-kantor { background: #e0f2fe; color: #0369a1; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: bold; }
</style>

<div class="container-fluid pb-5">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">Setup Otorisasi Cuti</h4>
            <p class="text-muted mb-0">
                @if ($cabangFilter)
                    Daftar pegawai pada <strong>{{ $cabangs[$cabangFilter] ?? '' }}</strong>
                @else
                    Daftar pegawai pada <strong>Semua Kantor</strong>
                @endif
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <span class="oi oi-check mr-2"></span> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="filter-section shadow-sm">
        <form method="GET" action="{{ route('setuser.index') }}">
            <div class="row align-items-end">
                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted text-uppercase">Cari Nama</label>
                    <input type="text" name="keyword" class="form-control form-control-custom" placeholder="Masukkan nama pegawai..."
                        value="{{ request('keyword') }}">
                </div>

                <div class="col-md-4 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted text-uppercase">Filter Cabang</label>
                    <select name="cabang" class="form-control form-control-custom">
                        <option value="">Semua Kantor</option>
                        @foreach ($cabangs as $id => $name)
                            <option value="{{ $id }}" @selected($cabangFilter == $id)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex">
                    <button class="btn btn-primary btn-rounded shadow-sm flex-grow-1 mr-2">
                        <span class="oi oi-magnifying-glass mr-1"></span> Filter
                    </button>

                    @if (request('keyword') || request('cabang'))
                        <a href="{{ route('setuser.index') }}" class="btn btn-outline-secondary btn-rounded flex-grow-1">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th class="text-left">Nama & Jabatan</th>
                        <th>Cabang</th>
                        <th>Atasan 1</th>
                        <th>Atasan 2</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $p)
                        <tr>
                            <td class="text-muted small align-middle">{{ $pegawai->firstItem() + $loop->index }}</td>
                            <td class="text-left align-middle">
                                <div class="font-weight-bold text-dark">{{ $p->name }}</div>
                                <small class="text-muted">{{ $p->relJabatan->name ?? '-' }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge-kantor shadow-sm">
                                    <span class="oi oi-map-marker mr-1 small"></span> {{ $p->relCabang->name ?? '-' }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="badge-atasan">
                                    {{ $p->atasan1_data->name ?? '-' }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="badge-atasan">
                                    {{ $p->atasan2_data->name ?? '-' }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('setuser.edit', $p->id) }}" class="btn btn-outline-primary btn-sm btn-rounded btn-block shadow-sm">
                                    Set User
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <span class="oi oi-info" style="font-size: 2rem; opacity: 0.2;"></span>
                                <p class="mt-2">Data pegawai tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $pegawai->appends(request()->query())->links() }}
    </div>
</div>
@endsection