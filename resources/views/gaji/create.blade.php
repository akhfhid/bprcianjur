@extends('layouts.global')
@section('title')@endsection

@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('gaji.store')}}" method="POST">
@csrf
	
	<label>Nama Pegawai</label>
	<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Tunjangan Jabatan</label>
	<input type="text" name="jabatan" class="form-control"><br>
	<label>Tunjangan Fungsional</label>
	<input type="text" name="fungsi" class="form-control"><br>
	
	<label>Tunjangan BPJS Kesehatan</label>
	<input type="text" class="form-control" name="bpjsks"><br>

	<label>Tunjangan BPJS Ketenagakerjaan</label>
	<input type="text" class="form-control" name="bpjstk"><br>
	
	<label>Tunjangan PPH Pasal 21</label>
	<input type="text" name="pph21" class="form-control"><br>

	
	<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pelatihan.index',$pegawai->id)}}" class="btn btn-primary"> Back </a>
</form>
</div>


@endsection