@extends('layouts.global')
@section('title')
    Log Pegawai
@endsection

@section('content')
    <link rel="stylesheet" href="{{ asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('DataTables/Buttons-1.5.6/css/buttons.bootstrap4.min.css') }}">

    <div class="row mb-4">

        <div class="col-md-2">
            <select id="periode" class="form-control">
                <option value="">-- Filter Periode --</option>
                <option value="quarter_1">Triwulan 1 (Jan–Mar)</option>
                <option value="quarter_2">Triwulan 2 (Apr–Jun)</option>
                <option value="quarter_3">Triwulan 3 (Jul–Sep)</option>
                <option value="quarter_4">Triwulan 4 (Okt–Des)</option>
                <option value="yearly">Tahunan (tahun ini)</option>
            </select>

        </div>

        <div class="col-md-2">
            <select id="tahun" class="form-control">
                <option value="">Pilih Tahun</option>
                @for ($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <select id="bulan" class="form-control">
                <option value="">Pilih Bulan</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>


        <div class="col-md-2">
            <input type="date" id="start_date" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" id="end_date" class="form-control">
        </div>

        <div class="col-md-2">
            <button id="applyFilter" class="btn btn-primary btn-block">
                Terapkan Filter
            </button>
        </div>

    </div>


    <table class="table table-bordered" id="table_log">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th>Waktu Akses</th>
            </tr>
        </thead>
    </table>


    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('DataTables/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('DataTables/pdfmake-0.1.36/vfs_fonts.js') }}"></script>
    <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.print.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            var table = $('#table_log').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/Loguser",
                    type: "GET",
                    data: function(d) {
                        d.periode = $('#periode').val();
                        d.tahun = $('#tahun').val();
                        d.bulan = $('#bulan').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: 'nampeg'
                    },
                    {
                        data: 'jenis'
                    },
                    {
                        data: 'keterangan'
                    },
                    {
                        data: 'created_at'
                    }
                ],
                order: [
                    [3, 'desc']
                ],
                dom: "<'row'<'col-md-4'l><'col-md-4'B><'col-md-4'f>>" +
                    "<'row'<'col-md-12'tr>>" +
                    "<'row'<'col-md-5'i><'col-md-7'p>>",
                buttons: ['copy', 'excel', 'pdf', 'print', 'colvis'],
                lengthMenu: [
                    [25, 50, 100, -1],
                    [25, 50, 100, "All"]
                ]
            });

            $('#applyFilter').click(function() {
                table.ajax.reload();
            });

        });
    </script>
@endsection
