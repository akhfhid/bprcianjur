@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">

<div class="col-md-8">
<h3 align="center">Form Permohonan Rotasi Pegawai</h3>
<hr class="my-3">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
<form action="{{route('supervisor.inputrotasi')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

<label>Nama Pegawai</label><br>
<input type="text" value="{{$pegawai->name}}" name="name" class="form-control" disabled="disabled">
<input type="hidden" value="{{$pegawai->id}}" name="idpeg" class="form-control">
<input type="text" value="{{$pegawai->cabang}}" name="cab" class="form-control" disabled="disabled">
<br>
<label>Jabatan</label>
<input type="text" name="jab" class="form-control" disabled="disabled" value="{{$jabatan}}"><br>
<label>Jabatan Untuk Rotasi</label>
<select class="form-control" name="jabatan">
	@foreach ($jabrot as $jabrots => $name)
	<option value="{{$jabrots}}">{{$name}}</option>
	@endforeach
</select><br>
<input type="submit" class="btn btn-primary" value="Submit">
</form>
</div>
@endsection