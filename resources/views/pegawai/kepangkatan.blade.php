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
				<a href="{{route('pegawai.jadwalberkala')}}" class="btn btn-primary">Jadwal Berkala Bulan Ini </a>
                <a href="{{route('pegawai.listpangkat')}}" class="btn btn-primary">Jadwal Pangkat Tahun Ini </a>
			</div>
		</div>



		<table class="table table-bordered table-stripped" id="listberkala">
			<thead>
				<tr align="center">
					<th><b>No</b></th>
					<th><b>Nama</b></th>
					<th><b>Cabang</b></th>
                    <th><b>Pangkat</b></th>
					<th><b>Jadwal Kenaikan Pangkat</b></th>
					<th><b>Jadwal Kenaikan Berkala</b></th>
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
table = $('#listberkala').DataTable({
 	processing: true,
 	ajax: {
 		url: '/Pegawai/BerkalaPangkat'
 	},
 	columns: [
 	{data: 'DT_RowIndex'},
	{data: 'nama'},
	{data: 'cabang'},
	//{data: 'jabatan'},
	{data: 'pangkat'},
	{data: 'jdpang'},
	{data: 'jdber'},
 	]
 });
});

    </script>


@endsection
