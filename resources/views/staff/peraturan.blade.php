@extends('layouts.global')

@section('title')
    Data Peraturan Staff
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
        }

        .modern-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* Buttons */
        .btn-modern {
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            transition: all 0.15s;
        }

        .btn-status {
            background-color: #fff;
            border: 1px solid #bae6fd;
            color: #0284c7;
        }

        .btn-status:hover {
            background-color: #f0f9ff;
            color: #0369a1;
        }

        .toggle-container {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 4px;
            display: inline-flex;
        }

        .toggle-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .toggle-btn.active {
            background-color: #ffffff;
            color: #2563eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        /* Select Wrapper */
        .select-wrapper {
            position: relative;
            width: 180px;
        }

        .select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 40px 10px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .select-wrapper select:hover {
            border-color: #9ca3af;
        }

        .select-wrapper select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .select-wrapper::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            font-size: 12px;
        }

        /* Table UI */
        .table-wrapper {
            overflow-x: auto;
        }

        #atur {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
        }

        #atur thead th {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            border-top: 0;
            font-weight: bold;
            color: #000000;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            padding: 14px 20px;
            text-align: left;
        }

        #atur thead th.text-center {
            text-align: center;
        }

        #atur tbody td {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        #atur tbody tr:hover {
            background-color: #f9fafb;
        }

        .action-col {
            white-space: nowrap;
            width: 130px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            color: #6b7280;
            transition: all 0.15s;
            border: 1px solid transparent;
            margin: 0 2px;
            text-decoration: none;
        }

        .action-btn:hover {
            background-color: #e5e7eb;
            color: #111827;
        }

        .action-btn.view:hover {
            color: #2563eb;
            border-color: #dbeafe;
        }

        /* DataTables Pagination */
        .dataTables_wrapper .dataTables_paginate {
            padding: 20px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 14px;
            margin: 0 2px;
            border-radius: 6px !important;
            background: #fff;
            border: 1px solid #e5e7eb !important;
            color: #374151 !important;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .nama-peraturan {
            max-width: 300px;
            white-space: normal;
            overflow: hidden;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .dataTables_wrapper .dataTables_info {
            padding-left: 20px;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 5px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }

        .alert-modern {
            border-radius: 10px;
            border-left: 4px solid #10b981;
            background-color: #ecfdf5;
            color: #065f46;
            padding: 16px 20px;
            font-size: 0.9rem;
        }
    </style>

    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-modern mb-4 d-flex align-items-center shadow-sm">
                <i class="fas fa-check-circle mr-3"></i> {{ session('status') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1">Manajemen Peraturan</h1>
                <p class="page-subtitle mb-0">Daftar Peraturan</p>
            </div>

        </div>

        <!-- Filters Card -->
        <div class="modern-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div class="toggle-container">
                        <button class="toggle-btn active" data-kategori="internal">
                            <i class="fas fa-building mr-2"></i> Internal
                        </button>
                        <button class="toggle-btn" data-kategori="external">
                            <i class="fas fa-globe mr-2"></i> External
                        </button>
                    </div>

                    <div class="d-flex gap-15px flex-wrap align-items-center">
                        <div class="select-wrapper">
                            <select id="filterJenis">
                                <option value="all">Semua Jenis</option>
                                <option value="SK">SK</option>
                                <option value="SE">SE</option>
                            </select>
                        </div>

                        <div class="select-wrapper" id="wrapperSubJenis" style="display: none;">
                            <select id="filterSubJenis">
                                <option value="all">Semua Sub Jenis</option>
                                <option value="POJK">POJK</option>
                                <option value="SEOJK">SEOJK</option>
                                <option value="PADK">PADK</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="modern-card">
            <div class="card-header bg-white border-0 py-3 px-4 border-bottom">
                <h6 class="font-weight-bold m-0 text-gray-700">Daftar Dokumen</h6>
            </div>

            <div class="table-wrapper">
                <table class="table mb-0" id="atur">
                    <thead>
                        <tr>
                            <th class="text-center">Nama Peraturan</th>
                            <th class="text-center">Nomor Peraturan</th>
                            <th class="text-center">Tanggal Peraturan</th>
                            <th class="text-center">Tanggal Berlaku</th>
                            <th id="colSubJenis" class="text-center" style="display:none;font-weight:bold;">Sub Jenis</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        let kategoriDipilih = "internal";
        let jenisDipilih = "all";
        let subJenisDipilih = "all";
        let table = null;

        function setFilterJenis() {
            let filter = $('#filterJenis');
            filter.empty();
            filter.append('<option value="all">Semua Jenis</option>');

            if (kategoriDipilih === "internal") {
                filter.append('<option value="SK">SK</option>');
                filter.append('<option value="SE">SE</option>');
            } else if (kategoriDipilih === "external") {
                filter.append('<option value="OJK">OJK</option>');
                filter.append('<option value="LPS">LPS</option>');
            }
        }

        function updateLayout() {
            let showSubJenis = (kategoriDipilih === "external" && jenisDipilih === "OJK");

            if (showSubJenis) {
                $('#wrapperSubJenis').slideDown();
                table.column(4).visible(true);
                $('#colSubJenis').show();
            } else {
                $('#wrapperSubJenis').slideUp();
                subJenisDipilih = "all";
                $('#filterSubJenis').val('all');
                table.column(4).visible(false);
                $('#colSubJenis').hide();
            }

            table.columns.adjust().draw();
        }

        $('.toggle-btn').click(function () {
            $('.toggle-btn').removeClass('active');
            $(this).addClass('active');

            kategoriDipilih = $(this).data('kategori');
            jenisDipilih = "all";
            subJenisDipilih = "all";

            setFilterJenis();
            $('#filterJenis').val('all');
            $('#filterSubJenis').val('all');

            if (table) {
                updateLayout();
                table.ajax.reload();
            }
        });

        $('#filterJenis').change(function () {
            jenisDipilih = $(this).val();
            subJenisDipilih = "all";
            $('#filterSubJenis').val('all');

            if (table) {
                updateLayout();
                table.ajax.reload();
            }
        });

        $('#filterSubJenis').change(function () {
            subJenisDipilih = $(this).val();
            if (table) table.ajax.reload();
        });

        function loadTable() {
            table = $('#atur').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('staff.peraturan') }}",
                    data: function (d) {
                        d.kategori = kategoriDipilih;
                        d.jenis_surat = jenisDipilih;
                        d.sub_jenis = subJenisDipilih;
                    }
                },
                columns: [
                    { data: "name", className: "nama-peraturan" },
                    { data: "nosk", className: "text-center" },
                    { data: "tglsk", className: "text-center" },
                    { data: "tgllaku", className: "text-center" },
                    { data: "jenis_ojk", className: "text-center", visible: false },
                    { data: "action", orderable: false, searchable: false, className: "text-center action-col" }
                ],
                order: [[2, 'desc']],
                language: {
                    emptyTable: "Tidak ada data peraturan",
                    processing: "<div class='py-5'><i class='fas fa-spinner fa-spin fa-2x text-primary'></i></div>"
                },
                initComplete: function () {
                    updateLayout();
                }
            });
        }

        loadTable();

    </script>
@endsection