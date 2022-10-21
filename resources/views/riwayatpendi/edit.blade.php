@extends('layouts.global')
@section('title') List Data Riwayat Pendidikan @endsection

@section('content')

<div class="row">
	<div class="col-md-8">
		<form enctype="multipart/form-data" method="POST" action="{{route('riwayatpendi.update',[$riwayatpendi->id])}}" class="p-3 shadow-sm bg-white">
			@csrf
			<input type="hidden" name="_method" value="PUT">
			<label for="name">Nama Pegawai</label><br>
			<input type="text" class="form-control" value="{{$pegawai->name}}" name="name" placeholder="Nama Pegawai" disabled="disable"><br>
			<input type="text" name="pegawai_id" value="{{$pegawai->id}}" hidden="hidden">
			<label>Tingkat Pendidikan</label>
	
	<select class="form-control" name="pendidikan">
		<option value="{{$riwayatpendi->pendidikan}}">{{$riwayatpendi->pendidikan}}</option>
		@foreach ($pendidikan as $pendidikans =>$name)
		<option value="{{$name}}">{{$name}}</option>
		@endforeach
	</select><br>
	<label>Nama Lembaga Pendidikan</label>
	<input type="text" name="name" class="form-control" value="{{$riwayatpendi->name}}"><br>
	<label>Fakultas/Jurusan</label>
	<input class="form-control" type="text" name="jurusan" value="{{$riwayatpendi->jurusan}}"><br>
	<label>Gelar</label>
	<input type="text" name="gelar" class="form-control" value="{{$riwayatpendi->gelar}}">
	<br>
	<label>Tahun Lulus</label>
	<input type="text" name="thnlulus" class="form-control" value="{{$riwayatpendi->thnlulus}}"><br>
	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('riwayatpendi.list',$pegawai->id)}}" class="btn btn-primary"> Back </a>
		</form>
	</div>
</div>


@endsection