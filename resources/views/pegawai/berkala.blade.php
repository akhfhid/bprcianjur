@extends('layouts.global')
@section('title') List Pegawai @endsection

@section('content')

<div class="row">
	<div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
<div class="row">
	<div class="col-md-8">
		<form enctype="multipart/form-data" method="POST" action="{{route('pegawai.updateberkala',[$pegawai->id])}}" class="p-3 shadow-sm bg-white">
			@csrf
			
			<label for="name">Nama</label><br>
			<input type="text" class="form-control" value="{{$pegawai->name}}" name="name" placeholder="Nama Pegawai"><br>
			<label for="tglberkala">Tanggal Berkala Sebelumnya</label><br>
			<input type="date" name="tglberkala" class="form-control" value="{{$pegawai->tglberkala}}"><br>

			<label for="tglpangkat">Tanggal Pangkat Sebelumnya</label><br>
			<input type="date" name="tglpangkat" class="form-control" value="{{$pegawai->tglpangkat}}"><br>
			
			<label for="tunda">Lama Penundaan</label><br>
			<input type="text" name="tunda" class="form-control" value="{{$pegawai->tunda}}"><br>
			<input type="submit" class="btn btn-primary" value="update">
			<a href="{{route('pegawai.show',$pegawai->id)}}" class="btn btn-primary"> Back </a>

		</form>
	</div>
</div>

@endsection