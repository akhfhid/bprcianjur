@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">
	<div class="col-md-12">
		<form action="{{route('pincab.rotasidisetujui')}}" >
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
		<a href="{{route('pincab.rotasiindex')}}" class="btn btn-primary">Kembali</a>
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
					<th><b>Cabang Mutasi</b></th>
					<th><b>Jabatan Mutasi</b></th>
					<th><b>Jenis Mutasi</b></th>
					<th><b>Action</b></th>
					
					
				</tr>
			</thead>
			<tbody>
				@foreach($mutasi as $mut)
				<tr>
					<td>{{$mut['name']}}</td>
					<td>{{$mut['cabmut']}}</td>
					<td>{{$mut['jabmut']}}</td>
					<td>{{$mut['jenis']}}</td>
					
					<td>
						<a href="{{route('pincab.detailpegawai',[$mut['pegawai_id']])}}" class="btn btn-info btn-sm">Profil</a>
						

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