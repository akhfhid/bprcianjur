@extends('layouts.global')

@section('title', 'Pegawai Tidak Aktif')

@section('content')
<style>
    /* UI Custom Styles konsisten dengan modul sebelumnya */
    .page-header { background: #fff; padding: 1.5rem; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #edf2f7; }
    .filter-section { background: #f8fafc; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
    
    .table-container { background: #fff; border-radius: 15px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.07); overflow: hidden; }
    .table thead th { background: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; border: none; padding: 15px; }
    .table td { padding: 12px 15px; vertical-align: middle !important; font-size: 0.85rem; }
    
    .btn-rounded { border-radius: 50px; padding-left: 1.2rem; padding-right: 1.2rem; }
    .form-control-custom { border-radius: 8px; border: 1px solid #e2e8f0; }
    
    /* Nav Pills Custom - Konsisten */
    .nav-pills .nav-link { border-radius: 50px; padding: 8px 20px; font-weight: 600; color: #64748b; margin-right: 10px; border: 1px solid transparent; }
    .nav-pills .nav-link.active { background-color: #dc3545 !important; color: #fff !important; box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2); }
    .nav-pills .nav-link:not(.active):hover { border-color: #cbd5e1; background: #f1f5f9; }
</style>

<div class="container-fluid pb-5">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">Arsip Pegawai (Trash)</h4>
            <p class="text-muted mb-0">Daftar pegawai yang sudah berhenti atau tidak aktif</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('pegawai.create') }}" class="btn btn-primary btn-rounded shadow-sm">
                <span class="oi oi-plus mr-2"></span> Tambah Pegawai
            </a>
        </div>
    </div>

    <div class="mb-4">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('pegawai.index') ? 'active' : '' }}" href="{{ route('pegawai.index') }}">
                    <span class="oi oi-people mr-2"></span> Aktif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('pegawai.trash') ? 'active' : '' }}" href="{{ route('pegawai.trash') }}">
                    <span class="oi oi-trash mr-2"></span> Berhenti/Tidak Aktif
                </a>
            </li>
        </ul>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <span class="oi oi-check mr-2"></span> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="filter-section shadow-sm">
        <form action="{{ route('pegawai.trash') }}">
            <div class="row align-items-end">
                <div class="col-md-9 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-muted text-uppercase">Cari di Arsip</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-custom" placeholder="Cari nama atau NIK..."
                            value="{{ Request::get('keyword') }}" name="keyword">
                        <div class="input-group-append">
                            <button class="btn btn-primary px-4" style="border-radius: 0 8px 8px 0;">
                                <span class="oi oi-magnifying-glass"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('pegawai.trash') }}" class="btn btn-outline-secondary btn-block btn-rounded">
                        Reset Filter
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th class="text-left">Nama & NIK</th>
                        <th>Status Peg</th>
                        <th>Masa Kerja</th>
                        <th>Jabatan</th>
                        <th>Kantor</th>
                        <th>Status Aktif</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pegawai as $pegawais)
                        <tr>
                            <td class="text-left align-middle">
                                <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $pegawais['name'] }}</div>
                                <small class="text-muted">NIK: {{ $pegawais['nikpegawai'] }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border px-2 py-1">{{ $pegawais['status'] }}</span>
                            </td>
                            <td class="align-middle">{{ $pegawais['mkerja'] }} Tahun</td>
                            <td class="align-middle text-dark font-weight-500">{{ $pegawais['jabatan'] }}</td>
                            <td class="align-middle text-muted">
                                <span class="oi oi-map-marker mr-1 small"></span> {{ $pegawais['cabang'] }}
                            </td>
                            <td class="align-middle">
                                <form method="POST" action="{{ route('pegawai.toggle-active', $pegawais['id']) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-rounded shadow-sm px-3 {{ $pegawais['status_active'] ? 'btn-success' : 'btn-secondary' }}" style="font-size: 0.7rem;">
                                        {{ $pegawais['status_active'] ? 'AKTIFKAN' : 'NONAKTIF' }}
                                    </button>
                                </form>
                            </td>
                            <td class="align-middle text-right">
                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus Data Pegawai Secara Permanen?')"
                                    action="{{ route('pegawai.delete-permanent', [$pegawais['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-rounded shadow-sm px-3">
                                        <span class="oi oi-trash mr-1"></span> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <span class="oi oi-info mb-2" style="font-size: 2rem; opacity: 0.2;"></span>
                                <p>Tidak ada data pegawai di arsip</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($deletedpegawai->hasPages())
        <div class="p-3 bg-light border-top">
            <div class="d-flex justify-content-center">
                {{ $deletedpegawai->appends(Request::all())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection