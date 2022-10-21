@extends('layouts.global')
@section('title')@endsection

@section('content')


<div class="col-md-8">
<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('gaji.update',[$gaji->id])}}" method="POST">
@csrf
	
	<input type="hidden" value="PUT" name="_method">
	
	<label>Nama Pegawai</label>
	<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

	<label>Tunjangan Jabatan</label>
	<input type="text" name="jabatan" class="form-control" value="{{$gaji->jabatan}}"><br>
	<label>Tunjangan Fungsional</label>
	<input type="text" name="fungsi" class="form-control" value="{{$gaji->fungsi}}">

	<label>Tunjangan BPJS Kesehatan</label>
	<input type="text" class="form-control" name="bpjsks" value="{{$gaji->bpjsks}}"><br>

	<label>Tunjangan BPJS Ketenagakerjaan</label>
	<input type="text" class="form-control" name="bpjstk" value="{{$gaji->bpjstk}}"><br>
	
	
	<label>Tunjangan PPH Pasal 21</label>
	<input type="text" name="pph21" class="form-control" value="{{$gaji->pph}}"><br>

	<input type="submit" class="btn btn-primary" value="Save">
			
</form>
</div>


@endsection