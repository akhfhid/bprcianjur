@extends('layouts.global')
@section('title') List Data Riwayat Pendidikan @endsection

@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('pelatihan.update',[$pelatihan->id])}}" method="POST">
@csrf
	<label>Nama Pegawai</label>
	<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Jenis Pelatihan</label>
	<input type="texy" class="form-control" name="name" value="{{$pelatihan->name}}"><br>

	<label>Lembaga Penyelenggara Pelatihan</label>
	<input type="text" class="form-control" name="penyelenggara" value="{{$pelatihan->penyelenggara}}"><br>
	
	<label>Tanggal Pelatihan</label>
	<input type="text" name="thnlatih" class="form-control" value="{{$pelatihan->thnlatih}}"><br>

	<label>Sertifikat Pelatihan</label><br>
	@if($pelatihan->image)
					<img src="{{asset('storage/'.$pelatihan->image)}}" width="70px">
					@else
					N/A
					@endif
					<br>
	<input type="file" class="form-control" name="image">
	<small class="text-muted">Kosongkan Jika Tidak Ingin Mengubah Sertifikat</small>
	<br>
	
	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pelatihan.list',$pegawai->id)}}" class="btn btn-primary"> Back </a>
</form>
</div>


@endsection