@extends('layouts.global')

@section('title') Data Peraturan @endsection

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .page-subtitle {
            font-size: .875rem;
            color: #8a94a6;
            margin-bottom: 1.5rem;
        }

        .kategori-group .btn {
            padding: .5rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            border-radius: 50px;
            margin-right: 5px;
            transition: all .2s;
        }

        .kategori-group .btn-outline-dark {
            color: #6c757d;
            border-color: #dee2e6;
            background-color: #fff;
        }

        .kategori-group .btn.active {
            background-color: #064b91 !important;
            color: #fff !important;
            border-color: #212529 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background-color: #f8f9fc;
            color: #6c757d;
            font-weight: 700;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 2px solid #e3e6f0;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            font-size: .9rem;
            border-bottom: 1px solid #e3e6f0;
        }
    </style>

    <div class="container-fluid py-4">

        @if(session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
            </div>
        @endif

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="page-title mb-0">Manajemen Peraturan</h1>
                <p class="page-subtitle mb-0 d-none d-sm-block">Ringkasan data dan dokumen peraturan instansi.</p>
            </div>

            <div>
                <a href="{{ route('peraturan.trash') }}" class="btn btn-outline-danger btn-sm shadow-sm mr-2">
                    <i class="fas fa-trash fa-sm mr-1"></i> Trash
                </a>
                <a href="{{ route('peraturan.create') }}" class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Baru
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="kategori-group btn-group">
                    <button type="button" class="btn btn-outline-dark active" data-kategori="internal">
                        <i class="fas fa-building mr-1"></i> Internal
                    </button>
                    <button type="button" class="btn btn-outline-dark" data-kategori="external">
                        <i class="fas fa-globe mr-1"></i> External
                    </button>
                </div>
            </div>
        </div>

        <div id="tableWrapper">

            <div class="card shadow-sm mb-4">

                <div class="card-header py-3 bg-white">
                    <div class="row align-items-center">

                        <div class="col-md-6">
                            <h6 class="m-0 font-weight-bold text-dark">Daftar Dokumen</h6>
                        </div>

                        <div class="col-md-6">

                            <div class="input-group input-group-sm" style="max-width:200px;float:right;">
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

                        <table class="table table-hover mb-0" id="atur">

                            <thead>
                                <tr>
                                    <th>Nama Peraturan</th>
                                    <th>Nomor Ketentuan</th>
                                    <th>Tanggal Ketentuan</th>
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

    <script src="{{asset('js/jquery-3.5.1.js')}}"></script>

    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

    <script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>

    <script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

    <script>

        let kategoriDipilih = "internal";
        let jenisDipilih = "all";
        let table = null;

        function setFilterJenis() {

            let filter = $('#filterJenis');
            filter.empty();

            filter.append('<option value="all">Semua Jenis</option>');

            if (kategoriDipilih === 'internal') {
                filter.append('<option value="SK">SK</option>');
                filter.append('<option value="SE">SE</option>');
            }

            if (kategoriDipilih === 'external') {
                filter.append('<option value="LPS">LPS</option>');
                filter.append('<option value="OJK">OJK</option>');
            }

        }

        $('.kategori-group .btn').click(function () {

            $('.kategori-group .btn').removeClass('active');
            $(this).addClass('active');

            kategoriDipilih = $(this).data('kategori');

            setFilterJenis();

            if (table) {
                table.ajax.reload();
            } else {
                loadTable();
            }

        });

        $('#filterJenis').change(function () {
            jenisDipilih = $(this).val();
            if (table) table.ajax.reload();
        });

        function loadTable() {

            table = $('#atur').DataTable({

                processing: true,
                serverSide: true,

                ajax: {
                    url: "/peraturan",
                    data: function (d) {
                        d.kategori = kategoriDipilih;
                        d.jenis_surat = jenisDipilih;
                    }
                },

                columns: [
                    { data: "name" },
                    { data: "nosk", className: "text-center" },
                    { data: "tglsk", className: "text-center" },
                    {
                        data: "action",
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }
                ],

                order: [[2, 'desc']]

            });

        }

        loadTable();

    </script>

@endsection