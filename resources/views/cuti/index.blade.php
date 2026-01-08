@extends('layouts.global')

@section('title')
    Data Cuti Pegawai
@endsection

@section('content')
    <style>
        /* UI Custom Styles agar konsisten dengan halaman detail */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
        }

        .filter-section {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .table-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .table thead th {
            background: #f1f5f9;
            color: #475569;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            border: none;
            padding: 15px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle !important;
        }

        .btn-rounded {
            border-radius: 50px;
            padding-left: 1.2rem;
            padding-right: 1.2rem;
        }

        .form-control-custom {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .badge-count {
            background: #e0e7ff;
            color: #4338ca;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.85rem;
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="page-header mb-4">
            <h4 class="font-weight-bold text-dark mb-1">Data Cuti Pegawai</h4>
            <p class="text-muted mb-0">Daftar akumulasi pengajuan cuti berdasarkan pegawai</p>
        </div>

        <div class="filter-section shadow-sm">
            <form method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted">CARI PEGAWAI</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-custom" placeholder="Masukkan nama pegawai...">
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="small font-weight-bold text-muted">KANTOR / CABANG</label>
                        <select name="cabang" class="form-control form-control-custom">
                            <option value="">-- Semua Kantor --</option>
                            @foreach ($cabangs as $id => $name)
                                <option value="{{ $id }}" {{ request('cabang') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-block btn-rounded shadow-sm">
                            <span class="oi oi-magnifying-glass mr-1"></span> Filter Data
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="text-center">
                        <tr>
                            <th width="60">No</th>
                            <th class="text-left">Nama Pegawai</th>
                            <th class="text-left">Sisa Cuti</th>
                            <th class="text-left">Kantor / Cabang</th>
                            <th>Total Pengajuan</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cutis as $pegawaiId => $items)
                            @php
                                $pegawai = optional($items->first())->pegawai;
                                $cabang = optional($pegawai)->relCabang;
                            @endphp

                            <tr>
                                <td class="text-center text-muted small">{{ $loop->iteration }}</td>

                                <td class="text-left">
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        {{ $pegawai->name ?? '-' }}
                                    </div>
                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">ID:
                                        {{ $pegawai->id ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $scuti = $pegawai->scuti ?? 0;

                                        if ($scuti > 7) {
                                            $color = '#16a34a'; // hijau
                                        } elseif ($scuti > 3) {
                                            $color = '#f59e0b'; // oren
                                        } else {
                                            $color = '#dc2626'; // merah
                                        }
                                    @endphp

                                    <div class="font-weight-bold" style="font-size:0.95rem; color: {{ $color }};">
                                        {{ $pegawai->scuti ?? '-' }}
                                    </div>

                                </td>

                                <td class="text-left">
                                    <span class="">
                                        <span class="oi oi-map-marker mr-1 "></span> {{ $cabang->name ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge-count">
                                        {{ $items->count() }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($pegawai)
                                        <a href="{{ route('cuti.pegawai', [
                                            'pegawai' => $pegawai->id,
                                            'cabang' => request('cabang'),
                                        ]) }}"
                                            class="btn btn-info btn-sm btn-rounded shadow-sm px-3">
                                            Detail
                                        </a>
                                    @else
                                        <span class="text-muted small italic">Pegawai Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <span class="oi oi-warning" style="font-size: 2rem; opacity: 0.3;"></span>
                                    </div>
                                    Tidak ada data cuti yang ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
