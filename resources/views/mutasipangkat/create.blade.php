@extends('layouts.global')
@section('title') Create Mutasi Pangkat @endsection
@section('footer-scripts')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script> 
				$('#pegawai').select2({ajax: { url: 'http://bprcianjur.test/ajax/pegawai/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	<script> 
				$('#pangkat').select2({ajax: { url: 'http://bprcianjur.test/ajax/pangkat/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
	
@endsection
@section ('title') Create Permohonan Mutasi Pangkat @endsection
@section ('content')


<div class="row">

	<div class="col-md-8">
		<h3 align="center">Form Permohonan Mutasi Pangkat Pegawai</h3>
		<hr class="my-3">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif

		<form action="{{route('mutasipangkat.store')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
			@csrf

<label>Nama Pegawai</label><br>
<select class="form-control" name="pegawai" id="pegawai">
</select>
<br>

<label>Pangkat</label><br>
<select class="form-control" name="pangkat" id="pangkat">
</select><br>


<label>Jenis Mutasi</label><br>
<select class="form-control" name="jenis" id="jenis">
<option value="">Pilih Jenis Mutasi</option>
<option value="DEMOSI">Demosi</option>
<option value="Promosi">Promosi</option>
</select><br>
<input type="submit" class="btn btn-primary" value="Save">
		</form>
	</div>
</div>
@endsection
