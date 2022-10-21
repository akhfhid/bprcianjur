@extends('layouts.global')
@section('title') Create Pangkat @endsection
@section('content')




<div class="col-md-8">
	@if(session('status'))
	<div class="alert alert-success">
		{{session('status')}}
	</div>
	@endif
	<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('pangkat.store')}}" method="POST">
		@csrf
		<label>Nama Pangkat</label><br>
		<input type="text" class="form-control" name="name"><br>
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
		<input type="submit" class="btn btn-primary" value="Save">

	</form>
</div>



@endsection