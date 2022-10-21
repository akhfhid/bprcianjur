@extends('layouts.global')
@section('title') List Data Riwayat Pelatihan @endsection

@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('pelatihan.store')}}" method="POST">
@csrf
	<label>Nama Pegawai</label>
	<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Jenis Pelatihan</label>
	<input type="texy" class="form-control" name="name"><br>

	<label>Lembaga Penyelenggara Pelatihan</label>
	<input type="text" class="form-control" name="penyelenggara"><br>
	
	<label>Tanggal Pelatihan</label>
	<input type="text" name="thnlatih" class="form-control"><br>

	<label>Sertifikat Pelatihan</label>
	<input type="file" class="form-control" name="image"><br>
	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pelatihan.index',$pegawai->id)}}" class="btn btn-primary"> Back </a>
</form>
</div>


@endsection