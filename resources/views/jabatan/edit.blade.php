@extends ('layouts.global')
@section('title') Edit Jabatan @endsection


@section('content')


@if(session('status'))
	<div class="alert alert-success">
		{{session('status')}}
	</div>
	@endif
<div class="col-md-8">

	<form action="{{route('jabatan.update',[$jabatan->id])}}" enctype="multipart/form-data" method="POST" class="bg-white shadow-sm p-3">

		@csrf

		<input type="hidden" value="PUT" name="_method">
		<label>Nama Jabatan</label>
		<input type="text" class="form-control" value="{{$jabatan->name}}" name="name">
		<br>

		<label>Kantor</label><br>
		<select name="kantor" class="form-control">
			<option value="{{$jabatan->kantor}}">Pilih Kantor</option>
			<option value="pusat">Kantor Pusat</option>
			<option value="cabang">Kantor Cabang</option>
		</select>
		<br>
		<label>Jabatan Atasan</label>
			<select class="form-control select2" name="atasan" id="atasan">
			<option value="{{$jabatan->atasan}}">{{$jabname}}</option>
                @foreach($atasan as $atas => $name)
                <option value="{{$atas}}">{{$name}}</option>
                @endforeach
            </select>
			<br>
			<br>
		<label>Tunjangan Program Pensiun</label>
		<input type="text" class="form-control" value="{{$jabatan->pensiun}}" name="pensiun">
		<br>
		<label>Tunjangan Istri / Suami</label>
		<input type="text" class="form-control" value="{{$jabatan->tunis}}" name="tunis">
		<br>
		<label>Tunjangan Anak</label>
		<input type="text" class="form-control" value="{{$jabatan->tunak}}" name="tunak">
		<br>
		<label>Tunjangan Pangan</label>
		<input type="text" class="form-control" value="{{$jabatan->tunpang}}" name="tunpang">
		<br>
		<label>Uang Makan</label>
		<input type="text" class="form-control" value="{{$jabatan->umak}}" name="umak">
		<br>

		<input type="submit" class="btn btn-primary" value="update">

	</form>
</div>
@endsection
