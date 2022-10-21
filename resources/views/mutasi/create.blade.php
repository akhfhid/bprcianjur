@extends('layouts.global')
@section('footer-scripts')
<link href="\node_modules\select2\dist\css\select2.min.css" rel="stylesheet" />
<script src="\node_modules\select2\dist\js\select2.min.js"></script>
<script> 
				$('#pegawai').select2({ajax: { url: 'http://bprcianjur.test/ajax/pegawai/search',
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
@section ('title') Create Permohonan Mutasi @endsection
@section ('content')


<div class="row">

	<div class="col-md-8">
		<h3 align="center">Form Permohonan Mutasi Pegawai</h3>
		<hr class="my-3">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif

		<form action="{{route('mutasi.store')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

<label>Nama Pegawai</label><br>
<select class="form-control" name="pegawai" id="pegawai">
</select>
<br>

<label>Jabatan</label><br>
<select class="form-control" name="jabatan" id="jabatan">
</select><br>

<label>Cabang</label><br>
<select class="form-control" name="cabang" id="cabang">
</select><br>

<label>Jenis Mutasi</label><br>
<select class="form-control" name="jenis" id="jenis">
<option value="">Pilih Jenis Mutasi</option>
<option value="ROTASI">Rotasi</option>
<option value="DEMOSI">Demosi</option>
<option value="Promosi">Promosi</option>
</select><br>
<input type="submit" class="btn btn-primary" value="Save">
		</form>
	</div>
</div>
@endsection