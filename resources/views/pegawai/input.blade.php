@extends('layouts.global')
@section('footer-scripts')

<link href="\node_modules\select2\dist\css\select2.min.css" rel="stylesheet" />
<script src="\node_modules\select2\dist\js\select2.min.js"></script>
<script> 
				$('#pangkat').select2({ajax: { url: 'http://bprcianjur.test/ajax/pangkat/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	<script> 
				$('#jabatan').select2({ajax: { url: 'http://bprcianjur.test/ajax/jabatan/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	<script> 
				$('#cabang').select2({ajax: { url: 'http://bprcianjur.test/ajax/cabang/search',
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
		<form action="{{route('pegawai.simpan')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

			<label>Nama</label><br>
			<input type="text" class="form-control" name="name" placeholder="Nama Pegawai">
			<br>
			<label>NIK Kepegawaian</label><br>
			<input type="text" class="form-control" name="nikpegawai" placeholder="NIK Kepegawaian"><br>
			<label>NIK Kependudukan</label>
			<input type="text" class="form-control" name="nikpenduduk" placeholder="NIK Kependudukan"><br>
			<label>Tempat Lahir</label><br>
			<input type="text" class="form-control" name="templahir" placeholder="Tempat Lahir"><br>
			<label>Tanggal Lahir</label><br>
			<input type="date" class="form-control" name="tgllahir"><br>
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
			<label>Status Perkawinan</label>
			<select class="form-control" name="status">
				<option value="#">Pilih Perkawinan</option>
				@foreach ($nikah as $nikahs => $name )
				<option value="{{$nikahs}}">{{$name}}</option>
				@endforeach
			</select><br>
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
				<option value="Kontrak">Pegawai Kontrak</option>
				<option value="Tetap">Pegawai Tetap</option>
			</select><br>
			<label>Kantor</label>
			<select class="form-control select2" name="cabang" id="cabang">
			</select><br>
			<label>Jabatan</label>
			<select class="form-control select2" name="jabatan" id="jabatan">
			</select><br>
			<label>Pangkat Kepegawaian</label>
				<select class="form-control select2" name="pangkat" id="pangkat">
				</select>
			<br>
				<label>Alamat Email</label><br>
			<input type="text" class="form-control" name="email" placeholder="Alamat Email"><br>
			<label for="photo"> Photo </label><br>
			<input type="file" class="form-control" name="photo"><br>
			<input type="submit" class="btn btn-primary" value="Save">
			</form>
		</div>
	</div>
</div>
</div>



@endsection