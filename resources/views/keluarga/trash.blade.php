@extends('layouts.global')
@section('title') Trashed Data Keluarga @endsection
@section('content')


<div class="row">
	<div class="col-md-6">
		<form action="{{route('keluarga.trash')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Keluarga" value="{{Request::get('keyword')}}" name="keyword">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
<div class="col-md-6">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link" href="{{route('keluarga.list',[$pegawai['id']])}}">Published</a>
			</li>
			<li class="nav-item">
			<a class="nav-link active" href="{{route('keluarga.trash')}}">Trash</a>
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
	
</div>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					
					<th><b>Nama Anggota Keluarga</b></th>
					<th><b>Hubungan</b></th>
					<th><b>Tempat Lahir</b></th>
					<th><b>Tanggal Lahir</b></th>
					<th><b>Umur</b></th>
					<th><b>Alamat</b></th>
					
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				
				@foreach ($keluarga as $datakeluarga)
				<td>{{$datakeluarga['name']}}</td>
				<td>{{$datakeluarga['hub']}}</td>
				<td>{{$datakeluarga['templahir']}}</td>
				<td>{{$datakeluarga['tgllahir']}}</td>
				<td>{{$datakeluarga['umur']}}</td>
				<td>{{$datakeluarga['alamat']}}</td>
				<td><a href="{{route('keluarga.edit',[$keluarga['id']])}}" class="btn btn-info btn-sm"> restore </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Move data keluarga to trash')" action="{{route('keluarga.destroy',[$keluarga['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="Trash" class="btn btn-danger btn-sm">
						</form><br>
				</td>


				@endforeach
			</tbody>
@endsection