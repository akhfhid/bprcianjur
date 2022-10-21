@extends('layouts.global')
@section('title') Create Kantor @endsection
@section('content')




<div class="col-md-8">
	@if(session('status'))
	<div class="alert alert-success">
		{{session('status')}}
	</div>
@endif
	<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('cabang.store')}}" method="POST">
		@csrf
		<label>Nama Kantor</label>
		<input type="text" class="form-control" name="name"/>
		<br>
		<label>Klasifikasi Kantor</label>
		<input type="text" class="form-control" name="class"/>
		<br>
		<label>Persentase Tunjangan Kinerja</label>
		<input type="text" class="form-control" name="tunjangan"/>
		<br>
		<input type="submit" class="btn btn-primary" value="Save">
	</form>
</div>


@endsection