@extends('layouts.global')
@section('title') Edit Data Pegawai @endsection
@section ('content')

<div class="row">
	<div class="col-md-8">
		<form enctype="multipart/form-data" method="POST" action="{{route('pegawai.update',[$pegawai->id])}}" class="p-3 shadow-sm bg-white">
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
			</select><br>

			<label for="alamat">Alamat</label>
			<textarea name="alamat" id="alamat" class="form-control" value="{{$pegawai->alamat}}">{{$pegawai->alamat}}</textarea><br>

			<label for="agama">Agama</label><br>
			<select class="form-control" name="agama">
				<option value="{{$pegawai->agama}}">{{$ag}}</option>
				@foreach ($agama as $agamas => $name)
				<option value="{{$agamas}}"> {{$name}}</option>
				@endforeach
			</select><br>
			<label>Golongan Darah</label>
			<select class="form-control" name="goldar">
				<option value="{{$pegawai->goldar}}">{{$pegawai->goldar}}</option>
				<option value="A">A</option>
				<option value="B">B</option>
				<option value="AB">AB</option>
				<option value="O">O</option>
			</select><br>
			<label for="status">Status Perkawinan</label><br>
			<select class="form-control" name="status">
				<option value="{{$pegawai->status}}">{{$stat}}</option>
				@foreach ($status as $kawin=>$name)
				<option value="{{$kawin}}">{{$name}}</option>
				@endforeach
			</select><br>
            <label>NPWP</label>
            <input type="text" class="form-control" name="npwp" value="{{$pegawai->npwp}}">
            <br>
            <label>No. Handphone</label>
            <input type="text" class="form-control" name="nohp" value="{{$pegawai->nohp}}"><br>
			<label for="pendidikan">Pendidikan Terakhir</label><br>
			<select class="form-control" name="pendidikan">
				<option value="{{$pegawai->pendidikan}}">{{$pend}}</option>
				@foreach ($pendidikan as $didik => $name)
				<option value="{{$didik}}">{{$name}}</option>
				@endforeach
			</select><br>

			<label for="tglmasuk">Tanggal Masuk</label>
			<input type="date" name="tglmasuk" class="form-control" value="{{$pegawai->tglmasuk}}"><br>

			<label>Status Pegawai</label>
			<select class="form-control" name="spegawai">
				<option value="{{$pegawai->spegawai}}">{{$spegawai}}</option>
				<@foreach ($tetap as $speg =>$name)
				<option class="form-control" value ="{{$speg}}">{{$name}}</option>
					@endforeach
			</select><br>

			<label for="kantor">Kantor</label><br>
			<select class="form-control" name="kantor">
				<option value="{{$pegawai->cabang}}">{{$kant}}</option>
				@foreach($kantor as $cabang=>$name)
				<option value="{{$cabang}}">{{$name}}</option>
				@endforeach
			</select><br>

			<label for="jabatan">Jabatan</label><br>
			<select class="form-control" name="jabatan">
				<option value="{{$pegawai->jabatan}}">{{$jab}}</option> 
				@foreach ($jabatan as $jabatans=> $name)
				<option value="{{$jabatans}}">{{$name}}</option>
				@endforeach
			</select><br>
			<label>Kepangkatan</label><br>
				<select class="form-control select2" name="pangkat">
					<option value="{{$pegawai->pangkat}}">{{$pang}}</option>
					@foreach ($pangkat as $pangkats => $name)
					<option value="{{$pangkats}}">{{$name}}</option>
					@endforeach
				</select>
			<br>
			<label> Masa Kerja Pangkat</label>
			<input type="text" class="form-control" name="mkpang" value="{{$pegawai->mkpang}}">
			<br>
			<label>Tunjangan Kinerja</label>
			<select class="form-control" name="tuncab">
			<option value="{{$pegawai->tuncab}}">{{$kant}}</option>
				<option value=""></option>
				@foreach($kantor as $cabang=>$name)
				<option value="{{$cabang}}">{{$name}}</option>
				@endforeach
			</select>
			<small class="text-muted">Kosongkan Jika Pegawai Belum Mendapatkan Tunjangan Kinerja</small><br><br><br>
			<label for="email">Alamat Email</label><br>
			<input type="text" class="form-control" name="email" value="{{$pegawai->email}}"><br>
			<label for="photo">Photo</label>
			<small class="text-muted">Current Photo</small><br>
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

