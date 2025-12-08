@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">

<div class="col-md-8">
<h3 align="center">Form Permohonan Cuti Pegawai</h3>
<hr class="my-3">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
<form action="{{route('kadiv.mintacuti')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

<label>Nama Pegawai</label><br>
<input type="text" value="{{$pega->name}}" name="name" class="form-control" disabled="disabled">
<input type="hidden" value="{{$pega->id}}" name="idpeg" class="form-control">
<br>
<label>Sisa Cuti Belum Diambil</label>
<input type="text" value="{{$pega->scuti}}" name="name" class="form-control" disabled="disabled">
<br>
<label>Tanggal Awal Cuti</label>
<input type="date" class="form-control" name="tglawal">
<br>
<label>Tanggal Akhir Cuti</label>
<input type="date" class="form-control" name="tglakhir">
<br>
<label>Alasan Cuti</label>
<textarea class="form-control" name="alasan"></textarea>
<br>
<input type="hidden" class="form-control" name="jeniscuti" value="Cuti Tahunan">
<input type="submit" class="btn btn-primary" value="Save">
</form>




@endsection
