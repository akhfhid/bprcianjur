@extends('layouts.global')
@section('footer-scripts')

<link href="\assets\select2\dist\css\select2.min.css" rel="stylesheet" />
<script src="\assets\select2\dist\js\select2.min.js"></script>
<script>
				$('#pangkat').select2({ajax: { url: '/ajax/pangkat/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	<script>
			$('#jabatan').select2({ajax: { url: '/ajax/jabatan/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	<script>
			$('#cabang').select2({ajax: { url: '/ajax/cabang/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	<script>
			$('#tuncab').select2({ajax: { url: '/ajax/cabang/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
@endsection

@section('title') Create Data Pegawai @endsection
@section('content')



<div class="row">
	<div class="col-md-8">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
		<form action="{{route('pegawai.store')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

			<label>Nama</label><br>
			<input value="{{old('name')}}" type="text" class="form-control {{$errors->first('name') ? "is-invalid": ""}}" name="name" placeholder="Nama Pegawai"/>
			<div class="invalid-feedback">
			{{$errors->first('name')}}
			</div>
			<br>
			<label>NIK Kepegawaian</label><br>
			<input value="{{old('nikpegawai')}}" type="text" class="form-control {{$errors->first('nikpegawai') ?"is-invalid": ""}}" name="nikpegawai" placeholder="NIK Kepegawaian">
			<div class="invalid-feedback">
			{{$errors->first('nikpegawai')}}
			</div><br>
			<label>NIK Kependudukan</label>
			<input value="{{old('nikpenduduk')}}" type="text" class="form-control {{$errors->first('nikpenduduk') ? "is-invalid" : ""}}" name="nikpenduduk" placeholder="NIK Kependudukan">
			<div class="invalid-feedback">
			{{$errors->first('nikpenduduk')}}
			</div>
			<br>

			<label>Tempat Lahir</label><br>
			<input value="{{old('templahir')}}" type="text" class="form-control {{$errors->first('templahir') ? "is-invalid" : ""}}" name="templahir" placeholder="Tempat Lahir">
			<div class="invalid-feedback">
			{{$errors->first('templahir')}}
			</div><br>

			<label>Tanggal Lahir</label><br>
			<input value="{{old('tgllahir')}}" type="date" class="form-control {{$errors->first('tgllahir')  ? "is-invalid" :""}} " name="tgllahir">

			<br>
			<label>Jenis Kelamin</label><br>
			<select class="form-control" name="jenkel">
				<option value="#">Pilih Jenis Kelamin</option>
				@foreach ($jenkel as $id => $name)
				<option value="{{$id}}">{{$name}}</option>
				@endforeach
			</select>
			<br>
			<label>Alamat</label>
			<textarea name="alamat" id="alamat" class="form-control" placeholder="Alamat Pegawai"></textarea><br>
			<label>Agama</label>
			<select class="form-control" name="agama">
				<option value="#">Pilih Agama</option>
				@foreach ($agama as $agamas => $name )
				<option value="{{$agamas}}">{{$name}}</option>
				@endforeach
			</select>
			<br>
			<label>Golongan Darah</label>
			<select class="form-control" name="goldar">
				<option value="#">Pilih Golongan Darah</option>
				<option value="A">A</option>
				<option value="B">B</option>
				<option value="AB">AB</option>
				<option value="O">O</option>
			</select>
			<br>
			<label>Status Perkawinan</label>
			<select class="form-control" name="status">
				<option value="#">Pilih Perkawinan</option>
				@foreach ($nikah as $nikahs => $name )
				<option value="{{$nikahs}}">{{$name}}</option>
				@endforeach
			</select><br>
            <label>NPWP</label>
            <input type="text" class="form-control" name="npwp" placeholder="NPWP">
            <br>
            <label>No. Handphone</label>
            <input type="text" class="form-control" name="nohp" placeholder="Nomor Handphone"><br>
			<label>Pendidikan Terakhir</label>
			<select class="form-control" name="pendidikan">
				<option value="#">Pilih Pendidikan</option>
				@foreach ($pendidikan as $pendidikans =>$name)
				<option value="{{$pendidikans}}">{{$name}}</option>
				@endforeach
				</select><br>
			<label>Tanggal Masuk Kerja</label>
			<input type="date" name="tglmasuk" class="form-control"><br>
			<label>Status Pegawai</label>
			<select class="form-control" name="spegawai">
				<option value="#">Pilih Status Pegawai</option>
				<@foreach ($statuspeg as $speg =>$name)
				<option value="{{$speg}}">{{$name}}</option>
					@endforeach
			</select><br>
			<label>Terhitung Mulai Tanggal</label>
			<input class="form-control" type="date" name="tglangkat">
			<br>
			<label>Kantor</label>
			<select class="form-control select2" name="cabang" id="cabang">
			</select><br>
			<label>Jabatan</label><br>
			<select class="form-control select2" name="jabatan" id="jabatan">
			</select><br>
			<label>Kepangkatan</label><br>
				<select class="form-control select2" name="pangkat" id="pangkat">
				</select>
				<br>
			<br>
			<label>Masa Kerja Pangkat</label>
			<input type="text" class="form-control" name="mkpang" id="mkpang">
			<br>
			<label>Tunjangan Kinerja</label>
			<select class="form-control select2" name="tuncab" id="tuncab">
			</select><br><br>
				<label>Alamat Email</label><br>
			<input value="{{old('email')}}" type="text" class="form-control {{$errors->first('email')  ? "is-invalid" :""}}" name="email" placeholder="Alamat Email">
			<div class="invalid-feedback">
			{{$errors->first('email')}}
			</div>
			<br>
			<label for="photo"> Photo </label><br>
			<input type="file" class="form-control" name="photo"><br>
			<input type="submit" class="btn btn-primary" value="Save">
			</form>
		</div>
	</div>
</div>
</div>



@endsection
