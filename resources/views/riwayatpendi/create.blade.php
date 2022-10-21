@extends('layouts.global');
@section('title') Create Riwayat Pendidikan @endsection
@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatpendi.store')}}" method="POST">
@csrf
	<label>Nama Pegawai</label>
	<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Tingkat Pendidikan</label>
	
	<select class="form-control" name="pendidikan">
		@foreach ($pendidikan as $pendidikans =>$name)
		<option value="{{$name}}">{{$name}}</option>
		@endforeach
	</select><br>
	<label>Nama Lembaga Pendidikan</label>
	<input type="text" name="name" class="form-control">
	<label>Fakultas/Jurusan</label>
	<input class="form-control" type="text" name="jurusan">
	<label>Gelar</label>
	<input type="text" name="gelar" class="form-control">
	<label>Tahun Lulus</label>
	<input type="text" name="thnlulus" class="form-control"><br>
	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
</form>
</div>
@endsection