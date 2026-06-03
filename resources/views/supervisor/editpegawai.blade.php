@extends('layouts.global')
@section('footer-scripts')
<link href="https:://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
	$('#pangkat').select2({ajax: {url: 'http://bprcianjur.test/ajax/pangkat/search', processResults: function(data){return {results: data.map(function(item){return {id: item.id, text:item.name} })
				}
			}
		}
	});
	var pang = {!! $pegawai->pangkat !!}
	pang.forEach(function(pangkat){
	var option = new Option(pangkat.name, pangkat.id, true, true);
	$('#pang').append(option).trigger('change');
	});
</script>
@endsection
@section('title') Edit Data Pegawai @endsection
@section ('content')

<div class="row">
	<div class="col-md-8">
		<form enctype="multipart/form-data" method="POST" action="{{route('supervisor.updatepegawai',[$pegawai->id])}}" class="p-3 shadow-sm bg-white">
			@csrf
			<input type="hidden" name="_method" value="PUT">
			<label for="name">Nama</label><br>
			<input type="text" class="form-control" value="{{$pegawai->name}}" name="name" placeholder="Nama Pegawai"><br>
			<label for="nikpegawai">NIK Kepegawaian</label><br>
			<input type="text" class="form-control" value="{{$pegawai->nikpegawai}}" name="nikpegawai" placeholder="NIK Kepegawaian"><br>
			<label for="nikpenduduk">NIK Kependudukan</label><br>
			<input type="text" class="form-control" value="{{$pegawai->nikpenduduk}}" name="nikpenduduk" placeholder="NIK KTP Pegawai"><br>
			<label for="tempatlahir">Tempat Lahir</label><br>
			<input type="text" name="templahir" class="form-control" value="{{$pegawai->templahir}}" placeholder="Tempat Lahir"><br>
			<label for="tgllahir">Tanggal Lahir</label><br>
			<input type="date" class="form-control" value="{{$pegawai->tgllahir}}" name="tgllahir"><br>
			<label for="Jenis Kelamin">Jenis Kelamin</label><br>

			<select class="form-control" name="jenkel">
			<option value="{{$pegawai->kelamin}}">{{$kel}}</option>	
				@foreach($jenkel as $kelamin => $name)
			<option value="{{$kelamin}}">{{$name}}</option>
				@endforeach
			</select>

			<label for="alamat">Alamat</label>
			<textarea name="alamat" id="alamat" class="form-control" value="{{$pegawai->alamat}}">{{$pegawai->alamat}}</textarea><br>

			<label for="agama">Agama</label><br>
			<select class="form-control" name="agama">
				<option value="{{$pegawai->agama}}">{{$ag}}</option>
				@foreach ($agama as $agamas => $name)
				<option value="{{$agamas}}"> {{$name}}</option>
				@endforeach
			</select><br>

			<label for="status">Status Perkawinan</label><br>
			<select class="form-control" name="status">
				<option value="{{$pegawai->status}}">{{$stat}}</option>
				@foreach ($status as $kawin=>$name)
				<option value="{{$kawin}}">{{$name}}</option>
				@endforeach
			</select><br>
			
			<label for="pendidikan">Pendidikan Terakhir</label><br>
			<select class="form-control" name="pendidikan">
				<option value="{{$pegawai->pendidikan}}">{{$pend}}</option>
				@foreach ($pendidikan as $didik => $name)
				<option value="{{$didik}}">{{$name}}</option>
				@endforeach
			</select><br>
			@if($pegawai->photo)
			<img src="{{asset('storage/'.$pegawai->photo )}}" width="96px">
			@endif<br><br>
			<input type="file" class="form-control" name="photo">
			<small class="text-muted">Kosongkan Jika tidak ingin mengubah photo</small><br><br>
			<input type="submit" class="btn btn-primary" value="update">
		</form>
	</div>
</div>


@endsection

