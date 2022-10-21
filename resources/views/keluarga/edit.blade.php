@extends('layouts.global')
@section('title') List Data Keluarga @endsection

@section('content')
<div class="row">
	<div class="col-md-8">
		<form enctype="multipart/form-data" method="POST" action="{{route('keluarga.update',[$keluarga->id])}}" class="p-3 shadow-sm bg-white">
			@csrf

			<input type="text" value="{{$pegawai->id}}" name="pegawai_id" hidden="hidden">		
			<label for="namekel">Nama Anggota Keluarga</label><br>
			<input type="text" name="name" class="form-control" value="{{$keluarga->name}}"><br>
			
			<label for="templahir">Tempat Lahir</label><br>
			<input type="text" name="templahir" class="form-control" value="{{$keluarga->templahir}}"><br>

			<label for="tgllahir">Tanggal Lahir</label><br>
			<input type="date" name="tgllahir" class="form-control" value="{{$keluarga->tgllahir}}"><br>

			<label>Alamat</label>
			<textarea name="alamat" id="alamat" class="form-control" placeholder="Alamat Pegawai">{{$keluarga->alamat}}</textarea><br>
			
			<label for="agama">Agama</label><br>
			<select class="form-control" name="agama">
				<option value="{{$keluarga->agama}}">{{$keluarga->agama}}</option>
				@foreach ($agama as $agamas => $name )
				<option value="{{$name}}">{{$name}}</option>
				@endforeach
			</select><br>
			<label>Status Perkawinan</label>
			<select class="form-control" name="status">
				<option value="{{$keluarga->skawin}}">{{$keluarga->skawin}}</option>
				@foreach ($nikah as $nikahs => $name )
				<option value="{{$name}}">{{$name}}</option>
				@endforeach
			</select><br>
			<label>Pekerjaan</label>
			<input type="text" name="pekerjaan" class="form-control" value="{{$keluarga->pekerjaan}}"><br>
			<label>Hubungan Keluarga</label>
			<select class="form-control" name="hubungan">
				<option value="{{$keluarga->hubungan}}">{{$keluarga->hubungan}}</option>
				@foreach ($hubkel as $hubkel => $name )
				<option value="{{$name}}">{{$name}}</option>
				@endforeach
			</select><br>
			<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('keluarga.list',[$pegawai->id])}}" class="btn btn-primary"> Back </a>
		</form>
		</div>
	</div>
@endsection