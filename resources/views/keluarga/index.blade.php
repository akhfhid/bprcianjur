@extends('layouts.global')
@section('title') List Data Keluarga @endsection

@section('content')

<div class="row">

	<div class="col-md-6">
		<form action="{{route('keluarga.list',[$pegawai['id']])}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Keluarga" value="{{Request::get('keyword')}}" name="keyword">
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
 </div>

<div class="row">

	<div class="col-md-12">
		<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('keluarga.tambah',$pegawai['id'])}}" class="btn btn-primary">Create Data Keluarga</a>
	</div>
</div>
		<b>Data Keluarga {{$pegawai['name']}}</b><br>

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
				
				@foreach ($datakeluarga as $keluarga)
				<tr>
				<td>{{$keluarga['name']}}</td>
				<td>{{$keluarga['hub']}}</td>
				<td>{{$keluarga['templahir']}}</td>
				<td>{{$keluarga['tgllahir']}}</td>
				<td>{{$keluarga['umur']}}</td>
				<td>{{$keluarga['alamat']}}</td>
				<td><a href="{{route('keluarga.edit',[$keluarga['id']])}}" class="btn btn-info btn-sm"> Edit </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('keluarga.delete-permanent',[$keluarga['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="Delete" class="btn btn-danger btn-sm">
						</form><br>
				</td>
			</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>




@endsection