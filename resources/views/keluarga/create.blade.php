@extends ('layouts.global')
@section('title')Create Anggota Keluarga @endsection
@section('content')


<div class="row">
	<div class="col-md-8">
		<form enctype="multipart/form-data" method="POST" action="{{route('keluarga.store')}}" class="p-3 shadow-sm bg-white">
			@csrf

			<label for="name">Nama Pegawai</label><br>
			<input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
			<input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>
			
			<label for="namekel">Nama Anggota Keluarga</label><br>
			<input type="text" name="name" class="form-control"><br>
			
			<label for="templahir">Tempat Lahir</label><br>
			<input type="text" name="templahir" class="form-control"><br>

			<label for="tgllahir">Tanggal Lahir</label><br>
			<input type="date" name="tgllahir" class="form-control"><br>

			<label>Alamat</label>
			<textarea name="alamat" id="alamat" class="form-control" placeholder="Alamat Pegawai"></textarea><br>
			
			<label for="agama">Agama</label><br>
			<select class="form-control" name="agama">
				@foreach ($agama as $agamas => $name )
				<option value="{{$name}}">{{$name}}</option>
				@endforeach
			</select><br>

			<label>Status Perkawinan</label>
			<select class="form-control" name="status">
				<option value="#">Pilih Perkawinan</option>
				@foreach ($nikah as $nikahs => $name )
				<option value="{{$name}}">{{$name}}</option>
				@endforeach
			</select><br>
			<label>Pekerjaan</label>
			<input type="text" name="pekerjaan" class="form-control"><br>
			<label>Hubungan Keluarga</label>
			<select class="form-control" name="hubungan">
				<option value="#">Pilih Hubungan</option>
				@foreach ($hubkel as $hubkel => $name )
				<option value="{{$name}}">{{$name}}</option>
				@endforeach
			</select><br>
			<input type="submit" class="btn btn-primary" value="Save">
			<a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
		</form>
		</div>
	</div>

@endsection

