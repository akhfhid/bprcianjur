@extends('layouts.global')
@section('title') List Pegawai @endsection

@section('content')
	
<div class="row">
	<div class="col-md-6">
		<form action="{{route('pegawai.index')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Pegawai" value="{{Request::get('keyword')}}" name="keyword">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
<div class="col-md-6">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link active" href="{{route('pegawai.index')}}">Published</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="{{route('pegawai.trash')}}">Trash</a>
		</li>
		</ul>
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
	<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('pegawai.create')}}" class="btn btn-primary">Create Data Pegawai</a>
	</div>
</div>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr>
					<th><b>Nama</b></th>
					<th><b>NIK Pegawai</b></th>
					<th><b>Status Pegawai</b></th>
					<th><b>Masa Kerja</b></th>
					<th><b>Pangkat</b></th>
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
					<td>{{$pegawais['pangkat']}}</td>
					<td>{{$pegawais['jabatan']}}</td>
					<td>{{$pegawais['cabang']}}</td>
					<td>
						<a href="{{route('pegawai.edit',[$pegawais['id']])}}" class="btn btn-info btn-sm" title="Edit Data Pegawai">Edit </a>
						<a href="{{route('pegawai.show',[$pegawais['id']])}}" class="btn btn-primary btn-sm" title="Detail Data Pegawai">Detail</a> 
						<form method="POST" class="d-inline" onsubmit="return confirm('Move data pegawai to trash')" action="{{route('pegawai.destroy',[$pegawais['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="X" class="btn btn-icon btn-danger" title="Hapus Data Pegawai">
						</form><br>
					</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
				<td colspan="10">
					{{$datapegawai->appends(Request::all())->links()}}
				</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
</div>



@endsection