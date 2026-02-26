@extends('layouts.global')

@section('title')
Status Permintaan Dokumen
@endsection

@section('content')
<div class="container-fluid py-4">

    @if (session('status'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm border-start border-4 border-warning" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                <div>
                    <strong>Perhatian!</strong><br>
                    {{ session('status') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-dark fw-bold">Monitoring Permintaan Dokumen</h4>
            <p class="text-muted small mt-1 mb-0">Pantau status pengajuan dan cetak dokumen yang telah disetujui.</p>
        </div>
        <a href="{{ route('pincab.peraturan') }}" class="btn btn-light border shadow-sm px-4 fw-medium">
            <i class="bi bi-arrow-left-circle me-2"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr class="bg-light text-dark">
                            {{-- 1. TAMBAHKAN HEADER NOMOR --}}
                            <th class="py-3 text-center fw-semibold text-uppercase small" style="width: 60px;">No</th>
                            <th class="py-3 px-4 fw-semibold text-uppercase small">Nama Peraturan</th>
                            <th class="py-3 text-center fw-semibold text-uppercase small">No SK</th>
                            <th class="py-3 text-center fw-semibold text-uppercase small">Tanggal</th>
                            <th class="py-3 fw-semibold text-uppercase small">Keperluan</th>
                            <th class="py-3 text-center fw-semibold text-uppercase small">Status</th>
                            <th class="py-3 text-center fw-semibold text-uppercase small">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @forelse ($orderatur as $atur)
                            @php
                                $status = strtoupper(trim($atur['status']));
                            @endphp

                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                {{-- 2. TAMPILKAN NOMOR OTOMATIS --}}
                                <td class="text-center fw-medium text-dark">
                                    {{ $loop->iteration + ($orderatur->currentPage() - 1) * $orderatur->perPage() }}
                                </td>
                                
                                <td class="px-4">
                                    <span class="fw-semibold text-dark d-block">{{ $atur['namepr'] }}</span>
                                </td>
                                <td class="text-center text-dark">{{ $atur['nosk'] }}</td>
                                <td class="text-center text-dark">{{ $atur['tglminta'] }}</td>
                                <td class="text-dark" style="max-width: 200px;">{{ Str::limit($atur['ket'], 30) }}</td>

                                {{-- STATUS BADGE --}}
                                <td class="text-center py-3">
                                    @if($status == "SUBMIT")
                                        <span class="badge rounded-pill px-3 py-2" 
                                              style="background-color: #FEF9C3; color: #854D0E; font-weight: 600; border: 1px solid #FDE68A;">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                        </span>
                                    @elseif(in_array($status, ["SETUJU","DISETUJUI","APPROVE"]))
                                        <span class="badge rounded-pill px-3 py-2" 
                                              style="background-color: #DCFCE7; color: #166534; font-weight: 600; border: 1px solid #BBF7D0;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                                        </span>
                                    @elseif(in_array($status, ["TOLAK","DITOLAK","REJECT"]))
                                        <span class="badge rounded-pill px-3 py-2" 
                                              style="background-color: #FEE2E2; color: #991B1B; font-weight: 600; border: 1px solid #FECACA;">
                                            <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-dark rounded-pill px-3 py-2 fw-semibold">
                                            {{ $status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center py-3">
                                    @if($status == "SUBMIT")
                                        <span class="text-muted small fst-italic">
                                            Menunggu proses...
                                        </span>

                                    @elseif(in_array($status, ["TOLAK","DITOLAK","REJECT"]))
                                        <span class="text-danger small fw-semibold">
                                            <i class="bi bi-file-earmark-x me-1"></i> Dibatalkan
                                        </span>

                                    @elseif(in_array($status, ["SETUJU","DISETUJUI","APPROVE"]))
                                        
                                        @if($atur['print'] == 'f')
                                            <a href="{{ route('pincab.show_pdf', $atur['idatur']) }}"
                                               class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm fw-medium"
                                               target="_blank"
                                               style="background: linear-gradient(135deg, #4F46E5, #4338CA); border: none;">
                                               <i class="bi bi-printer-fill me-1"></i> Print
                                            </a>
                                        @else
                                            <span class="badge rounded-pill px-3 py-2"
                                                  style="background-color: #F3F4F6; color: #374151; font-weight: 600; border: 1px solid #E5E7EB; box-shadow: none;">
                                                <i class="bi bi-check2-all text-success me-1"></i> Selesai Dicetak
                                            </span>
                                        @endif

                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Sesuaikan colspan menjadi 7 karena ada 1 kolom baru --}}
                                <td colspan="7" class="text-center py-5 bg-light">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-inbox text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <p class="text-muted mt-2 mb-0">Tidak ada data permintaan dokumen</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($orderatur->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orderatur->links('pagination::bootstrap-4') }} 
        </div>
    @endif

</div>
@endsection