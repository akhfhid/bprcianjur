@extends('layouts.global')

@section('title') Data Peraturan @endsection

@section('content')
<!-- Tambahkan Font Awesome & Google Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Reset & Base Style */
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fc;
        color: #333;
    }

    /* Card Style */
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    /* Header Title Style */
    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }
    
    .page-subtitle {
        font-size: 0.875rem;
        color: #8a94a6;
        margin-bottom: 1.5rem;
    }

    /* Style untuk Tombol Kategori (Button Group) */
    .kategori-group .btn {
        padding: 0.5rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 50px; /* Pill shape */
        margin-right: 5px;
        transition: all 0.2s;
    }

    .kategori-group .btn-outline-dark {
        color: #6c757d;
        border-color: #dee2e6;
        background-color: #fff;
    }

    .kategori-group .btn-outline-dark:hover {
        background-color: #f8f9fa;
        color: #333;
    }

    /* Style Saat Tombol Aktif */
    .kategori-group .btn.active {
        background-color: #212529 !important;
        color: #fff !important;
        border-color: #212529 !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    /* Statistik Card */
    .stat-card {
        border-radius: 8px;
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    /* Warna Border Statistik */
    .stat-total { border-color: #4e73df; }
    .stat-sk { border-color: #36b9cc; }
    .stat-se { border-color: #1cc88a; }
    .stat-tahun { border-color: #6c757d; }

    /* Table Header */
    .table thead th {
        background-color: #f8f9fc;
        color: #6c757d;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        padding: 1rem;
    }

    /* Table Body */
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #e3e6f0;
    }

    /* Action Buttons in Table */
    .action-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        border-radius: 4px;
    }

</style>

<div class="container-fluid py-4">

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- HEADER SECTION -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title mb-0">Manajemen Peraturan</h1>
            <p class="page-subtitle mb-0 d-none d-sm-block">Ringkasan data dan dokumen peraturan instansi.</p>
        </div>
        <a href="{{route('peraturan.create')}}" class="btn btn-primary btn-sm shadow-sm mt-3 mt-sm-0">
            <i class="fas fa-plus fa-sm mr-2"></i> Tambah Baru
        </a>
    </div>

    <!-- KATEGORI PILIHAN (Nav Pills Style) -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="kategori-group btn-group" role="group">
                        <button type="button" class="btn btn-outline-dark active" data-kategori="internal">
                            <i class="fas fa-building mr-1"></i> Internal
                        </button>
                        <button type="button" class="btn btn-outline-dark" data-kategori="external">
                            <i class="fas fa-globe mr-1"></i> External
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="row no-gutters align-items-center">
                        <div class="col">
                             <!-- Filter bisa diletakkan di sini atau di dalam card header tabel -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIK ROW -->
    <div class="row mb-4" id="statistikRow" style="display:none;">
        <!-- Total -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-total shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Peraturan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statTotal">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SK -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-sk shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total SK</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statSK">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SE -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-se shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total SE</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statSE">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope-open-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tahun Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-tahun shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-gray-800 text-uppercase mb-1">Tahun {{date('Y')}}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statTahun">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE WRAPPER -->
    <div id="tableWrapper" style="display:none;">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 bg-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="m-0 font-weight-bold text-dark">Daftar Dokumen</h6>
                    </div>
                    <div class="col-md-6">
                         <div class="input-group input-group-sm" style="max-width: 200px; float:right;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0">
                                    <i class="fas fa-filter text-muted"></i>
                                </span>
                            </div>
                            <select id="filterJenis" class="form-control form-control-sm border-left-0 pl-0">
                                <option value="all">Semua Jenis</option>
                                <option value="SK">SK</option>
                                <option value="SE">SE</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="atur" width="100%">
                        <thead>
                            <tr>
                                <th>Nama Peraturan</th>
                                <th>Nomor SK</th>
                                <th>Tanggal SK</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL DELETE -->
<div id="confirmModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Hapus Data?</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3" style="opacity: 0.2;"></i>
                <p class="mb-0 small text-muted">Data yang dihapus tidak dapat dikembalikan. Yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer border-0 bg-light py-2">
                <button class="btn btn-light btn-sm" data-dismiss="modal">Batal</button>
                <button id="ok_button" class="btn btn-danger btn-sm font-weight-bold">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{asset('js/jquery-3.5.1.js')}}"></script>
<script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

<script>
    function loadStatistik(){
        $.get('/peraturan/statistik', function(res){
            $('#statistikRow').fadeIn('fast');
            $('#statTotal').text(res.total);
            $('#statSK').text(res.sk);
            $('#statSE').text(res.se);
            $('#statTahun').text(res.tahun_ini);
        });
    }

    let kategoriDipilih = null;
    let jenisDipilih = 'all';
    let table = null;

    // Event Listener untuk Tombol Kategori Kecil
    $('.kategori-group .btn').click(function(){
        // 1. Manage Active State UI
        $('.kategori-group .btn').removeClass('active');
        $(this).addClass('active');

        // 2. Set Value & Load Data
        kategoriDipilih = $(this).data('kategori');
        
        // Tampilkan wrapper dengan efek mulus
        $('#tableWrapper').fadeIn('fast');
        
        loadStatistik();
        loadTable();
    });

    $('#filterJenis').change(function(){
        jenisDipilih = $(this).val();
        if(table) table.ajax.reload();
    });

    function loadTable(){
        if(table !== null){
            table.destroy();
        }

        table = $('#atur').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            "language": {
                "processing": "<div class='text-center p-3'><i class='fas fa-spinner fa-spin fa-2x text-primary'></i><br>Memuat data...</div>"
            },
            ajax: {
                url: "/peraturan",
                data: function(d){
                    d.kategori = kategoriDipilih;
                    d.jenis_surat = jenisDipilih;
                }
            },
            columns: [
                {data: "name"},
                {data: "nosk", className: "text-center"},
                {data: "tglsk", className: "text-center"},
                {
                    data: "action", 
                    orderable:false, 
                    searchable:false,
                    className: "text-center"
                }
            ],
            order:[[2,'desc']]
        });
    }

    let atur_id;
    $(document).on('click','.delete',function(){
        atur_id = $(this).attr('id');
        $('#confirmModal').modal('show');
    });

    $('#ok_button').click(function(){
        $.ajax({
            url:"peraturan/destroy/"+atur_id,
            beforeSend:function(){
                $('#ok_button').html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            },
            success:function(){
                $('#confirmModal').modal('hide');
                table.ajax.reload();
                alert('Data berhasil dihapus'); 
            },
            error: function(){
                 $('#ok_button').html('<i class="fas fa-check mr-1"></i> Ya, Hapus');
                 alert('Terjadi kesalahan.');
            }
        });
    });
</script>
@endsection