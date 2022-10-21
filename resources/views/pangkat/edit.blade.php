@extends('layouts.global')
@section('title') Edit Pangkat @endsection
@section('content')
<div class="col-md-8">
	<form action="{{route('pangkat.update',[$pangkat->id])}}" enctype="multipart/form-data" method="POST" class="bg-white shadow-sm p-3">
		@csrf
		<input type="hidden" Value="PUT" name="_method">
		
		<label>Nama Pangkat</label><br>
		<input type="text" class="form-control" value="{{$pangkat->name}}" name="name">
		<br>
		<label>Pendidikan Minimal</label>
		<select class="form-control" name="pendmin">
			<option>Pilih Pendidikan</option>
			@foreach ($pendidikan as $pend => $name)
			<option value="{{$pend}}">{{$name}}</option>
			@endforeach
		</select><br>
		<label>Pendidikan Maksimal</label>
		<select class="form-control" name="pendmax">
			<option>Pilih Pendidikan</option>
			@foreach ($pendidikan as $pend => $name)
			<option value="{{$pend}}">{{$name}}</option>
			@endforeach
		</select><br>
		<br>
		<input type="submit" class="btn btn-primary" name="Update">


	</form>
</div>
@endsection