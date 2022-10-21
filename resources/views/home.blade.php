@extends('layouts.global')
@section("title") Home @endsection
@section('content')
    <link rel="stylesheet" href="{{asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">

    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" align="center">SIKAP</div>

                <div class="card-body" align="center">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    Selamat Datang
                    Sistem Informasi Kepegawaian dan Peraturan
                </div>
            </div>
        </div>
    </div>
    </div>
    @if(auth()->user()->roles == 'ADMIN')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" align="center">Pangkat</div>

                <div class="card-body" align="center">
                    <table class="table table-bordered table-striped" id="pangkat">
                        <thead>
                        <tr align="center">
                            <th>No</th>
                            <th>Nama</th>
                            <th>Cabang</th>
                            <th>Tanggal Kenaikan Pangkat</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" align="center">Berkala</div>

                <div class="card-body" align="center">
                    <table class="table table-bordered table-striped" id="berkala">
                        <thead>
                        <tr align="center">
                            <th>No</th>
                            <th>Nama</th>
                            <th>Cabang</th>
                            <th>Tanggal Kenaikan Berkala</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" align="center">Update Peraturan</div>
                <div class="card-body" align="justify">
            <table class="table table-bordered table-stripped">

            <tbody>
                <tr>
                    @foreach ($peraturan as $atur)

                        <td>{{$atur->name}} telah ditambahkan pada tanggal {{$atur->created_at ->format('d-m-Y')}}</td>
                            <td>
                                @if(auth()->user()->roles == 'KADIV')
                                <a href="{{route('kadiv.showatur',$atur->id)}}" class="btn btn-primary">Detail</a>
                                @elseif(auth()->user()->roles == 'USER')
                                <a href="{{route('staff.showatur',$atur->id)}}" class="btn btn-primary">Detail</a>
                                @elseif(auth()->user()->roles == 'PINCAB')
                                <a href="{{route('pincab.showatur',$atur->id)}}" class="btn btn-primary">Detail</a>
                                @elseif(auth()->user()->roles == 'SUPERVISOR')
                                <a href="{{route('supervisor.showatur',$atur->id)}}" class="btn btn-primary">Detail</a>
                                @elseif(auth()->user()->roles == 'PATUH')
                                <a href="{{route('peraturan.show',$atur->id)}}" class="btn btn-primary">Detail</a>
                                @elseif(auth()->user()->roles == 'DIRUT')
                                <a href="{{route('supervisor.showatur',$atur->id)}}" class="btn btn-primary">Detail</a>
                                @endif
                            </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>


                    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
                    <script src="{{asset('js/jquery-3.5.1.js')}}"></script>
                    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>

                    <!-- Datatables -->
                    <script src="{{asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js')}}"></script>
                    <script src="{{asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js')}}"></script>

                    <script type="text/javascript">
                        let table;
                        $(function(){
                            table = $('#pangkat').DataTable({
                                pageLength : 1,
                                processing: true,
                                ajax: {
                                    url: '/Pegawai/ListPangkat'
                                },
                                columns: [
                                    {data: 'DT_RowIndex'},
                                    {data: 'nama'},
                                    {data: 'cabang'},
                                    {data: 'jdpang'},
                                ],
                            });
                        });
                        $(function(){
                            table = $('#berkala').DataTable({
                                pageLength : 1,
                                processing: true,
                                ajax: {
                                    url: '/pegawai/data'
                                },
                                columns: [
                                    {data: 'DT_RowIndex'},
                                    {data: 'nama'},
                                    {data: 'cabang'},
                                    {data: 'jdber'},
                                ],
                            });
                        });
                    </script>


@endsection
