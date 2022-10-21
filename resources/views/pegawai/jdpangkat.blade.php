@extends('layouts.global')
@section('title')Jadwal @endsection
@section ('content')
    <link rel="stylesheet" href="{{asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">

    <div class="row">

    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            @if(session('status'))
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            {{session('status')}}
                        </div>
                    </div>
                </div>
            @endif
                <div class="row mb-3">
                    <div class="col-md-12 text-right">
                        <a href="{{route('pegawai.listberkala')}}" class="btn btn-primary">Kembali </a>

                    </div>
                </div>
            <table class="table table-bordered table-stripped" id="listpangkat">
                <thead>
                <tr align="center">
                    <th><b>No</b></th>
                    <th><b>Nama</b></th>
                    <th><b>Cabang</b></th>
                    <th><b>Pangkat/Golongan</b></th>
                    <th><b>Jadwal Kenaikan Pangkat</b></th>

                </tr>
                </thead>
            </table>

        </div>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{asset('js/jquery-3.5.1.js')}}"></script>
        <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

        <!-- Datatables -->
        <script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

        <script type="text/javascript">
            let table;
            $(function(){
                table = $('#listpangkat').DataTable({
                    processing: true,
                    ajax: {
                        url: '/Pegawai/ListPangkat'
                    },
                    columns: [
                        {data: 'DT_RowIndex'},
                        {data: 'nama'},
                        {data: 'cabang'},
                        {data : 'pangkat'},
                        {data: 'jdpang'},
                        ],
                });
            });

        </script>


@endsection
