@extends('layouts.global')
@section('title')Data Peraturan @endsection
@section ('content')


<hr class="my-3">

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
		<a href="{{route('supervisor.peraturan')}}" class="btn btn-primary">Kembali</a>
	</div>
</div>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					<th><b>Nama Peraturan</b></th>
					<th><b>Nomor Surat Keputusan</b></th>
					<th><b>Tanggal Permintaan Data</b></th>
					<th><b>Keperluan Permintaan Data</b></th>
					<th><b>Status</b></th>
					<th><b>Actions</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($orderatur as $atur)
				<tr>
					<td>{{$atur['namepr']}}</td>
					<td>{{$atur['nosk']}}</td>
					<td>{{$atur['tglminta']}}</td>
					<td>{{$atur['ket']}}</td>
					<td>@if($atur['status'] =="SUBMIT")
						<span class="badge bg-warning text-light">{{$atur['status']}}</span>
						@elseif($atur['status'] =="SETUJU")
						<span class="badge bg-success text-light">{{$atur['status']}}</span>
						@elseif($atur['status'] =="TOLAK")
						<span class="badge bg-info text-light">{{$atur['status']}}</span>
						@elseif($atur['status'] =="DIBATALKAN")
						<span class="badge bg-dark text-light">{{$atur['status']}}</span>
						@endif</td>
					<td>
						@if($atur['status']=="SETUJU")
						<a href="{{route('supervisor.showatur',$atur['idatur'])}}" class="btn btn-primary">Detail</a>
						@endif
					</td>
				</tr>
			</tbody>
			@endforeach
		</table>

</div>


@endsection