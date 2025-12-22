@extends('layouts.global')

@section('title','List Permohonan Cuti')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --soft-bg: #f8fafc;
        --border-color: #e2e8f0;
    }

    body { background-color: #f1f5f9; }

    /* Layout Cards */
    .card-custom {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    /* Header Styling */
    .page-title {
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.025em;
    }

    /* Filter Section */
    .filter-wrapper {
        padding: 1.5rem;
        background: #ffffff;
        border-radius: 12px;
    }

    .form-label-small {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: block;
    }

    .input-modern {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }

    .input-modern:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    /* Status & Pills */
    .filter-pills-group {
        display: flex;
        gap: 8px;
        background: #f1f5f9;
        padding: 5px;
        border-radius: 10px;
        width: fit-content;
    }

    .pill-item {
        padding: 6px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pill-item.active {
        background: #ffffff;
        color: var(--primary);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        border-top: none;
        padding: 1rem;
    }

    .table-modern tbody td {
        vertical-align: middle;
        padding: 1rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }

    .employee-name { font-weight: 600; color: #1e293b; margin-bottom: 0; }
    .office-name { font-size: 0.75rem; color: #94a3b8; }

    /* Status Badge Modern */
    .badge-soft {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-block;
    }
    .status-submit { background: #fef3c7; color: #92400e; }
    .status-disetujui { background: #dcfce7; color: #166534; }
    .status-ditolak { background: #fee2e2; color: #991b1b; }

    /* Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    /* Loading Effect */
    .loading-shimmer { opacity: 0.5; pointer-events: none; transition: 0.3s; }
</style>

<div class="container-fluid py-4">
    
    {{-- TITLE & HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="page-title mb-0">Manajemen Cuti</h3>
            <p class="text-muted small mb-0">Kelola dan tinjau seluruh permohonan cuti pegawai</p>
        </div>
        
        @if(request()->has('status') || request()->has('jenis') || request()->has('name') || request()->has('tanggal'))
            <a href="{{ route('ordercuti.index') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <span class="oi oi-reload mr-1"></span> Reset Semua Filter
            </a>
        @endif
    </div>

    {{-- FILTER CARD --}}
    <div class="card-custom">
        <div class="filter-wrapper">
            <form id="filterForm" action="{{ route('ordercuti.indexcuti') }}" method="GET">
                <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">

                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label-small">Cari Pegawai</label>
                        <div class="input-group">
                            <input name="name" value="{{ request('name') }}"
                                   class="form-control input-modern"
                                   placeholder="Ketik nama...">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="form-label-small">Tanggal Mulai</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                               class="form-control input-modern">
                    </div>

                    <div class="col-lg-5 col-md-12 mb-3">
                        <label class="form-label-small">Status Cuti</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('ordercuti.index', array_merge(request()->except('status'), ['status' => 'DISETUJUI'])) }}" 
                               class="btn btn-sm {{ request('status') == 'DISETUJUI' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3">
                               Disetujui
                            </a>
                            <a href="{{ route('ordercuti.index', array_merge(request()->except('status'), ['status' => 'DITOLAK'])) }}" 
                               class="btn btn-sm {{ request('status') == 'DITOLAK' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
                               Ditolak
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 ml-auto">
                                <span class="oi oi-magnifying-glass"></span> Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex align-items-center gap-3">
                    <span class="form-label-small mb-0 mr-2">Kategori:</span>
                    <div class="filter-pills-group">
                        <a href="{{ route('ordercuti.index', request()->except('jenis')) }}" 
                           class="pill-item {{ !request('jenis') ? 'active' : '' }}">Semua</a>
                        
                        @foreach(['Cuti Tahunan','Cuti Wajib','Cuti Lainnya'] as $j)
                            <a href="{{ route('ordercuti.index', array_merge(request()->all(),['jenis'=>$j])) }}"
                               class="pill-item {{ request('jenis') == $j ? 'active' : '' }}">
                               {{ $j }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card-custom overflow-hidden">
        <div class="table-responsive" id="tableResult">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="60">No</th>
                        <th>Pegawai</th>
                        <th>Jenis & Alasan</th>
                        <th class="text-center">Durasi</th>
                        <th>Periode</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no=1; @endphp
                    @forelse($orderc as $o)
                    <tr>
                        <td class="text-center text-muted font-weight-bold">{{ $no++ }}</td>
                        <td>
                            <p class="employee-name">{{ $o['namapeg'] }}</p>
                            <span class="office-name"><span class="oi oi-map-marker mr-1"></span>{{ $o['namacab'] }}</span>
                        </td>
                        <td>
                            <span class="badge badge-light border mb-1">{{ $o['jenis'] }}</span>
                            <p class="small text-muted mb-0 text-truncate" style="max-width: 180px;">{{ $o['alasan'] }}</p>
                        </td>
                        <td class="text-center">
                            <span class="text-primary font-weight-bold">{{ $o['jmlcuti'] }} Hari</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="small">
                                    <div class="font-weight-bold">{{ \Carbon\Carbon::parse($o['tglawal'])->format('d M Y') }}</div>
                                    <div class="text-muted">{{ \Carbon\Carbon::parse($o['tglakhir'])->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = 'status-submit';
                                if($o['status']=='DISETUJUI') $statusClass = 'status-disetujui';
                                elseif($o['status']=='DITOLAK') $statusClass = 'status-ditolak';
                            @endphp
                            <span class="badge-soft {{ $statusClass }}">{{ $o['status'] }}</span>
                        </td>
                        <td class="text-center">
                            @if($o['statdiket']=='DISETUJUI' && $o['status']=='SUBMIT')
                                <div class="d-flex justify-content-center gap-2">
                                    <form method="POST" action="{{ route('ordercuti.setuju',$o['id']) }}">
                                        @csrf
                                        <button class="btn btn-success btn-sm px-3 rounded-pill" title="Setujui">
                                            Setuju
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('ordercuti.tolak',$o['id']) }}">
                                        @csrf
                                        <button class="btn btn-outline-danger btn-sm px-3 rounded-pill" title="Tolak">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small"><span class="oi oi-check mr-1"></span>Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center">
                            <img src="https://illustrations.popsy.co/gray/status-not-found.svg" style="width: 120px;" class="mb-3 opacity-50">
                            <p class="text-muted">Tidak ada data permohonan yang sesuai filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if(isset($ordercuti) && $ordercuti->hasPages())
            <div class="p-4 bg-light border-top">
                {{ $ordercuti->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tetap mempertahankan logic original Anda agar tidak merusak fungsionalitas
    $('#filterForm').on('submit',function(e){
        e.preventDefault();
        loadData($(this).serialize());
    });

    $(document).on('click','#tableResult .pagination a',function(e){
        e.preventDefault();
        loadData($(this).attr('href').split('?')[1]);
    });

    function loadData(q){
        $.ajax({
            url:"{{ route('ordercuti.indexcuti') }}",
            data:q,
            beforeSend: function(){
                $('#tableResult').addClass('loading-shimmer');
            },
            success: function(res){
                $('#tableResult').html(res).removeClass('loading-shimmer');
            }
        });
    }
</script>
@endpush