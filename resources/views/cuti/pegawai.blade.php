@extends('layouts.global')

@section('title')
    Detail Cuti Pegawai
@endsection

@section('content')
    <style>
        /* UI Custom Styles */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #edf2f7;
        }

        .filter-section {
            background: #f8fafc;
            padding: 1rem;
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
            padding: 12px;
        }

        /* Stepper Approval Style */
        .approval-flow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #cbd5e1;
        }

        .step-line {
            width: 15px;
            height: 2px;
            background: #e2e8f0;
        }

        /* Badge Styles */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-block;
            min-width: 80px;
        }

        .bg-submit {
            background: #fef3c7;
            color: #92400e;
        }

        .bg-disetujui {
            background: #dcfce7;
            color: #166534;
        }

        .bg-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Perbaikan Tombol Kembali Agar Selalu Muncul */
        .btn-kembali {
            background-color: #6c757d !important;
            color: white !important;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            text-decoration: none !important;
        }

        .btn-kembali:hover {
            background-color: #5a6268 !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h4 class="font-weight-bold text-dark mb-1">Riwayat Cuti</h4>
                <p class="text-muted mb-0">Pegawai: <span class="text-primary font-weight-bold">{{ $pegawai->name }}</span>
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('cuti.index', ['cabang' => request('cabang')]) }}" class="btn-kembali shadow-sm">
                    <span class="oi oi-arrow-left mr-2" style="font-size: 0.8rem;"></span> Kembali ke Daftar
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="filter-section shadow-sm">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="btn-group flex-wrap">
                        <a href="{{ route('cuti.pegawai', ['pegawai' => $pegawai->id, 'cabang' => request('cabang')]) }}"
                            class="btn btn-sm btn-rounded mr-2 mb-2 {{ !request('jenis') ? 'btn-primary' : 'btn-white border' }}"
                            style="border-radius: 50px;">Semua</a>
                        @foreach (['TAHUNAN' => 'Tahunan', 'WAJIB' => 'Wajib', 'LAINNYA' => 'Lainnya'] as $val => $label)
                            <a href="{{ route('cuti.pegawai', ['pegawai' => $pegawai->id, 'jenis' => $val, 'cabang' => request('cabang'), 'q' => request('q')]) }}"
                                class="btn btn-sm btn-rounded mr-2 mb-2 {{ request('jenis') == $val ? 'btn-primary' : 'btn-white border' }}"
                                style="border-radius: 50px;">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4">
                    <form method="GET" class="input-group">
                        <input type="hidden" name="cabang" value="{{ request('cabang') }}">
                        <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-sm" style="border-radius: 50px 0 0 50px;"
                            placeholder="Cari...">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm px-3" style="border-radius: 0 50px 50px 0;">Cari</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-container shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th class="text-left">Jenis & Tanggal</th>
                            <th>Durasi</th>
                            <th class="text-left">Alasan</th>
                            <th>Atasan 1</th>
                            <th>Atasan 2</th>
                            <th>Alur Approval</th>
                            <th>Status Akhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($cutis as $cuti)
                            @php
                                $isWajib = \Illuminate\Support\Str::contains(strtoupper($cuti->jeniscuti), 'WAJIB');
                                $atasan1 = $atasanPegawaiMap[$cuti->otoatasan] ?? null;
                                $atasan2 = $atasanPegawaiMap[$cuti->diketatasan] ?? null;
                            @endphp

                            <tr>
                                <td class="align-middle text-muted">
                                    {{ ($cutis->currentPage() - 1) * $cutis->perPage() + $loop->iteration }}
                                </td>

                                <td class="align-middle text-left">
                                    <div class="font-weight-bold">{{ $cuti->jeniscuti }}</div>
                                    <small class="text-muted">{{ $cuti->tglawal }} - {{ $cuti->tglakhir }}</small>
                                </td>

                                <td class="align-middle">
                                    <span class="badge badge-light border px-2 py-1">
                                        {{ $cuti->jmlcuti }} Hari
                                    </span>
                                </td>

                                <td class="align-middle text-left">
                                    <small class="text-dark">{{ $cuti->alasan ?: '-' }}</small>
                                </td>

                                <td class="align-middle text-left">
                                    <div class="font-weight-bold">
                                        {{ $atasan1->name ?? '-' }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.65rem">
                                        {{ $atasan1->relJabatan->name ?? '' }}
                                    </div>
                                </td>

                                <td class="align-middle text-left">
                                    <div class="font-weight-bold">
                                        {{ $atasan2->name ?? '-' }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.65rem">
                                        {{ $atasan2->relJabatan->name ?? '' }}
                                    </div>
                                </td>

                                <td class="align-middle">
                                    <div class="approval-flow">
                                        <span
                                            class="step-dot
                    {{ $cuti->statasan == 'DISETUJUI' ? 'bg-success' : ($cuti->statasan == 'DITOLAK' ? 'bg-danger' : 'bg-warning') }}"
                                            title="Atasan 1">
                                        </span>

                                        <div class="step-line"></div>

                                        <span
                                            class="step-dot
                    {{ $cuti->statdiket == 'DISETUJUI' ? 'bg-success' : ($cuti->statdiket == 'DITOLAK' ? 'bg-danger' : 'bg-warning') }}"
                                            title="Atasan 2">
                                        </span>

                                        @if ($isWajib)
                                            <div class="step-line"></div>
                                            @php
                                                $sdmStatus = 'bg-warning';
                                                if (!empty($cuti->otosdm)) {
                                                    $sdmStatus = 'bg-success';
                                                }
                                                if ($cuti->statsdm == 'DITOLAK') {
                                                    $sdmStatus = 'bg-danger';
                                                }
                                            @endphp
                                            <span class="step-dot {{ $sdmStatus }}"
                                                title="SDM: {{ $cuti->otosdm ?: 'Menunggu' }}">
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td class="align-middle">
                                    <span class="status-badge bg-{{ strtolower($cuti->status) }} shadow-sm">
                                        {{ $cuti->status }}
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td class="align-middle">
                                    <div class="btn-group">
                                        <a href="{{ route('cuti.edit', $cuti->id) }}"
                                            class="btn btn-sm btn-light border-0 shadow-sm mr-1" style="border-radius: 8px;"
                                            title="Edit">
                                            <span class="oi oi-pencil text-warning"></span>
                                        </a>

                                        {{-- Tombol pemicu modal --}}
                                        <button type="button" class="btn btn-sm btn-light border-0 shadow-sm"
                                            style="border-radius: 8px;" data-toggle="modal"
                                            data-target="#hapusCutiModal{{ $cuti->id }}" title="Hapus">
                                            <span class="oi oi-trash text-danger"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="hapusCutiModal{{ $cuti->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content border-0 shadow" style="border-radius: 15px;">

                                        <form action="{{ route('cuti.destroy', $cuti->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="redirect_to" value="{{ url()->full() }}">

                                            <div class="modal-header border-0 pt-4 px-4">
                                                <h5 class="font-weight-bold text-dark">Konfirmasi Hapus</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body px-4">
                                                <div class="alert alert-warning border-0 small mb-3"
                                                    style="background: #fffbeb; color: #92400e; border-radius: 10px;">
                                                    <span class="oi oi-info mr-2"></span> Data ini akan dihapus dari
                                                    riwayat aktif (Soft Delete).
                                                </div>

                                                <p class="text-muted mb-3">Anda akan menghapus pengajuan
                                                    <strong>{{ $cuti->jeniscuti }}</strong>. Mohon berikan alasan
                                                    penghapusan:</p>

                                                <div class="form-group">
                                                    <textarea name="alasan_hapus" class="form-control border-light shadow-sm" rows="3"
                                                        style="border-radius: 10px; background: #f8fafc;" placeholder="Tulis alasan di sini (misal: Salah input data)..."
                                                        required></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer border-0 pb-4 px-4">
                                                <button type="button" class="btn btn-light px-4" data-dismiss="modal"
                                                    style="border-radius: 50px; font-weight: 500;">Batal</button>
                                                <button type="submit" class="btn btn-danger px-4 shadow-sm"
                                                    style="border-radius: 50px; font-weight: 500;">
                                                    Hapus Data
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="py-5 text-center text-muted">
                                    Data riwayat cuti tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $cutis->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
