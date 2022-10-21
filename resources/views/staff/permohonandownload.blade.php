@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">

<div class="col-md-8">
<h3 align="center">Form Permohonan Unduh Data Peraturan Perusahaan</h3>
<hr class="my-3">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
<form action="{{route('staff.mintadownload')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

<label>Nama Pegawai</label><br>
<input type="text" value="{{$pegawai->name}}" name="name" class="form-control" disabled="disabled">
<input type="hidden" value="{{$pegawai->id}}" name="idpeg" class="form-control">
<input type="hidden" value="{{$pegawai->cabang}}" name="cabang" class="form-control"><br>
<label>Nama Peraturan</label><br>
<input type="text" value="{{$peraturan->name}}" name="name" class="form-control" disabled="disabled">
<input type="hidden" value="{{$peraturan->id}}" name="idperaturan" class="form-control">
<br>
<label>Tujuan Penggunaan</label>
<textarea class="form-control" name="ket"></textarea>
<br>
<input type="submit" class="btn btn-primary" value="Save">
</form>




@endsection