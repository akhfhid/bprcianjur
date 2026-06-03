@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">
	<div class="col-md-12">
		<form action="{{route('direksi.mutasipangkat')}}" >
			<div class="row">
				<div class="col-md-5">
					<input value="{{Request::get('name')}}" name="name" type="text" class="form-control" placeholder="Search By Name">
				</div>
				<div class="col-md-2">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>

		</form>
		<div class="row">
	<div class="col-md-12">

<hr class="my-3">
<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="#" class="btn btn-primary">Disetujui</a>
		<a href="#" class="btn btn-primary">Ditolak</a>
	</div>
</div>
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>

@endif
		<table class="table table-stripped table-bordered">
			<thead>
				<tr align="center">
					
					<th><b>Nama Pegawai</b></th>
					<th><b>Masa Kerja</b></th>
					<th><b>Pangkat Sebelum</b></th>
					<th><b>Pangkat Mutasi</b></th>
					<th><b>Status</b></th>
					<th><b>Action</b></th>
					
				</tr>
			</thead>
			<tbody>
				@foreach($mutasipangkat as $mp)
				<tr>
					<td>{{$mp['namapeg']}}</td>
					<td>{{$mp['mkerja']}}</td>
					<td>{{$mp['pangseb']}}</td>
					<td>{{$mp['pangkat']}}</td>
					<td>{{$mp['jenis']}}</td>
					
					<td align="center">
						<a href="{{route('pegawai.show',[$mp['pegawai_id']])}}" class="btn btn-info btn-sm">Profil</a>
						<form method="POST" class="d-inline" onsubmit="return confirm('Setujui Permohonan Mutasi Pegawai')" action="{{route('direksi.setujupangkat',[$mp['id']])}}">
							@csrf
							<input type="submit" value="Setuju" class="btn btn-success btn-sm">
						</form>
						<form method="POST" class="d-inline" onsubmit="return confirm('Tolak Permohonan Mutasi Pegawai')" action="{{route('direksi.tolakpangkat',[$mp['id']])}}">
							@csrf
							<input type="submit" value="Tolak" class="btn btn-danger btn-sm">
						</form>

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