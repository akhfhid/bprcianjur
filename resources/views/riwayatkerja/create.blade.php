@extends('layouts.global')
@section('title') List Data Riwayat Pendidikan @endsection

@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatkerja.store')}}" method="POST">
@csrf
	<label>Nama Pegawai</label>
	<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Jabatan</label>
	<input type="text" name="jabatan" class="form-control"> 
	<br>
	<label>Kantor</label>
	<select class="form-control" name="cabang">
		@foreach ($cabang as $cabangs =>$name) 
		<option value="{{$name}}">{{$name}}</option>
		@endforeach
	</select><br>
	<label>Tanggal Awal Jabatan</label>
	<input type="date" name="tglawal" class="form-control"><br>
	<label>Tanggal Akhir Jabatan</label>
	<input type="date" name="tglakhir" class="form-control"><br>
	
	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>   
</form>
</div>


@endsection