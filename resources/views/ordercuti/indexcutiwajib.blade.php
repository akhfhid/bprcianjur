@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">
	<div class="col-md-12">
		<form action="{{route('ordercuti.indexcutiwajib')}}" >
			<div class="row">
				<div class="col-md-5">
					<input value="{{Request::get('name')}}" name="name" type="text" class="form-control" placeholder="Search By Name">
				</div>
					<div class="col-md-5">
					<input value="{{Request::get('date')}}" name="tanggal" type="date" class="form-control" placeholder="Search By Date">
				</div>
				<div class="col-md-2">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>

		</form>
		<div class="row">
	<div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>

@endif
<hr class="my-3">
<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('ordercuti.indexcutitahunan')}}" class="btn btn-primary">Cuti Tahunan</a>
        <a href="{{route('ordercuti.indexcutiwajib')}}" class="btn btn-primary">Cuti Wajib</a>
        <a href="{{route('ordercuti.indexcutilainnya')}}" class="btn btn-primary">Cuti Lainnya</a>
		<a href="{{route('ordercuti.datasetuju')}}" class="btn btn-primary">Disetujui</a>
		<a href="{{route('ordercuti.datatolak')}}" class="btn btn-primary">Ditolak</a>
	</div>
</div>
		<table class="table table-stripped table-bordered">
			<thead>
				<tr align="center">

					<th><b>Nama Pegawai</b></th>
					<th><b>Kantor</b></th>
                    <th><b>Jenis Cuti</b></th>
					<th><b>Jumlah Cuti</b></th>
					<th><b>Tanggal Awal Cuti</b></th>
					<th><b>Tanggal Akhir Cuti</b></th>
					<th><b>Alasan Cuti</b></th>
					<th><b>Status</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach($orderc as $order)
				<tr align="center">

					<td>{{$order['namapeg']}}</td>
					<td>{{$order['namacab']}}</td>
                    <td>{{$order['jenis']}}</td>
					<td>{{$order['jmlcuti']}} hari</td>
					<td>{{$order['tglawal']}}</td>
					<td>{{$order['tglakhir']}}</td>
					<td>{{$order['alasan']}}</td>
					<td>
						@if($order['status'] =="SUBMIT")
						<span class="badge bg-warning text-light">{{$order['status']}}</span>
						@elseif($order['status'] =="DISETUJUI")
						<span class="badge bg-success text-light">{{$order['status']}}</span>
						@elseif($order['status'] =="DITOLAK")
						<span class="badge bg-info text-light">{{$order['status']}}</span>
						@elseif($order['status'] =="DIBATALKAN")
						<span class="badge bg-dark text-light">{{$order['status']}}</span>
						@endif
					</td>
					<td>

						@if($order['statdiket']== "DISETUJUI" &&$order['status']== "SUBMIT" && $order['otosdm'] == "ADMIN")
						<form method="POST" class="d-inline" onsubmit="return confirm('Setujui Permohonan Cuti')" action="{{route('ordercuti.setuju',[$order['id']])}}">
							@csrf
							<input type="submit" value="Setuju" class="btn btn-success btn-sm">
						</form>
						<form method="POST" class="d-inline" onsubmit="return confirm('Tolak Permohonan Cuti')" action="{{route('ordercuti.tolak',[$order['id']])}}">
							@csrf
							<input type="submit" value="Tolak" class="btn btn-danger btn-sm">
						</form>
						
						<a href="{{route('ordercuti.edit',[$order['id']])}}" class="btn btn-info btn-sm">Edit</a>
						@endif
						<br>


					</td>
				</tr>
			</tbody>
			@endforeach
			<tfoot>
				<tr>
				<td colspan="10">
					{{$indexcuti->appends(Request::all())->links()}}
				</td>
				</tr>
			</tfoot>
			</tfoot>
		</table>

	</div>
</div>

@endsection
