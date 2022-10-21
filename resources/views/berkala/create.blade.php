@extends('layouts.global')
@section('title') Create Golongan @endsection
@section('content')




<div class="col-md-8">
	@if(session('status'))
	<div class="alert alert-success">
		{{session('status')}}
	</div>
	@endif
	<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('berkala.store')}}" method="POST">
		@csrf
		<label>Nama Pangkat</label><br>
		<input type="text" class="form-control" name="pangkat" value="{{$pangkat->name}}" disabled="disabled">
		<input type="hidden" name="idpang" class="form-control" value="{{$pangkat->id}}"><br>
		<label>Golongan</label>
		<input type="text" name="gol" class="form-control"><br>
		<label>Gaji Pokok</label>
		<input type="text" name="gapok" class="form-control"><br>

		
		<input type="submit" class="btn btn-primary" value="Save">

	</form>
</div>



@endsection