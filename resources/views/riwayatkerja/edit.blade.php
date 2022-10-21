@extends('layouts.global')
@section('title')@endsection

@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatkerja.update',[$riwayatkerja->id])}}" method="POST">
@csrf
	<label>Nama Pegawai</label>
	<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Jabatan</label>
	<input type="text" name="jabatan" class="form-control" value="{{$riwayatkerja->name}}"> 
	<br>
	<label>Kantor</label>
	<select class="form-control" name="cabang">
		<option value="{{$riwayatkerja->kantorcabang}}">{{$riwayatkerja->kantorcabang}}</option>
		@foreach ($cabang as $cabangs =>$name)
		<option value="{{$name}}">{{$name}}</option>
		@endforeach
	</select><br>
	<label>Tanggal Awal</label>
	<input type="text" name="tglawal" class="form-control" value="{{$riwayatkerja->tglawal}}"><br>
	<label>Tanggal Akhir</label>
	<input type="text" name="tglakhir" class="form-control" value="{{$riwayatkerja->tglakhir}}"><br>

	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
</form>
</div>


@endsection
