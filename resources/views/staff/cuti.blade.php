@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')


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
		<a href="{{route('staff.permohonancuti')}}" class="btn btn-primary">Permohonan Cuti Tahunan</a>
		<a href="{{route('staff.cutiwajib')}}" class="btn btn-primary">Permohonan Cuti Wajib</a>
		<a href="{{route('staff.cutilainnya')}}" class="btn btn-primary">Permohonan Lainnya</a>
		<a href="{{route('staff.cutisetuju')}}" class="btn btn-primary">Disetujui</a>
		<a href="{{route('staff.cutitolak')}}" class="btn btn-primary">Ditolak</a>
	</div>
</div>
		<table class="table table-stripped table-bordered">
			<thead>
				<tr align="center">
					
					<th><b>Nama Pegawai</b></th>
					<th><b>Kantor Cabang</b></th>
					<th><b>Jumlah Cuti</b></th>
					<th><b>Tanggal Awal Cuti</b></th>
					<th><b>Tanggal Akhir Cuti</b></th>
					<th><b>Alasan Cuti</b></th>
					<th><b>Status</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach($orderc as $order)
				<tr>
					
					<td>{{$order['namapeg']}}</td>
					<td>{{$order['namacab']}}</td>
					<td>{{$order['jmlcuti']}}</td>
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

					</td>
				</tr>
			</tbody>
			@endforeach
			<tfoot>
				<tr>
				<td colspan="10">
				</td>
			</tr>
			</tfoot>
		</table>
		
	</div>
</div>

@endsection