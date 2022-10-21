@extends('layouts.global')
@section('title') List Pegawai @endsection

@section('content')
	
<div class="row">
	<div class="col-md-6">
		<form action="{{route('kepatuhan.indexpegawai')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Pegawai" value="{{Request::get('keyword')}}" name="keyword">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
</div>
<hr class="my-3">
<div class="row">
	<div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
	
		<table class="table table-bordered table-stripped">
			<thead>
				<tr>
					<th><b>Nama</b></th>
					<th><b>NIK Pegawai</b></th>
					<th><b>Status Pegawai</b></th>
					<th><b>Masa Kerja</b></th>
					<th><b>Jabatan</b></th>
					<th><b>Kantor</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach($pegawai as $pegawais)
				<tr>
					<td>{{$pegawais['name']}}</td>
					<td>{{$pegawais['nikpegawai']}}</td>
					<td>{{$pegawais['status']}}</td>
					<td>{{$pegawais['mkerja']}} Tahun</td>
					<td>{{$pegawais['jabatan']}}</td>
					<td>{{$pegawais['cabang']}}</td>
					<td>
						<a href="{{route('kepatuhan.detailpegawai',[$pegawais['id']])}}" class="btn btn-icon btn-warning" title="Detail Data Pegawai"><i class="far fa-user"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
				<td colspan="10">

				</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
</div>



@endsection