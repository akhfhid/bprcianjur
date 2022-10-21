@extends ('layouts.global')
@section ('title') Edit Kantor @endsection
@section ('content')

@if(session('status'))
<div class="alert alert-success">
	{{session('status')}}
</div>
@endif
<div class="col-md-8">
	<form action="{{route('cabang.update',[$cabang->id])}}" enctype="multipart/form-data" method="POST" class="bg-white shadow-sm p-3">
		@csrf

		<input type="hidden" value="PUT" name="_method">
		<label>Nama Kantor</label><br>
		<input type="text" class="form-control" value="{{$cabang->name}}" name="name">
		<br>
		<label>Klasifikasi Kantor</label>
		<input type="text" class="form-control" value="{{$cabang->class}}" name="class"/>
		<br>
		<label>Persentase Tunjangan Kinerja</label>
		<input type="text" class="form-control" value="{{$cabang->tunjangan}}"name="tunjangan"/>
		<br><br>
		<input type="submit" class="btn btn-primary" value="Update">
	</form>
</div>
@endsection