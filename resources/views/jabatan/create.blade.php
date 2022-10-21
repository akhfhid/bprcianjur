@extends('layouts.global')
@section('title') Create Jabatan @endsection
@section('footer-scripts')

<link href="/assets/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="/assets/select2/dist/js/select2.min.js"></script>

	<script>
				$('#atasan').select2({ajax: { url: '/ajax/jabatan/search',
processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
					}
				}
			}
		});
	</script>
@endsection



@section('content')

<?php
 header("Access-Control-Allow-Origin: *");
 ?>

@if (session('status'))
	<div class="alert alert-success">
		{{session('status')}}
	</div>
	@endif

<div class="col-md-8">

	<form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('jabatan.store')}}" method="POST">
		@csrf
		<label>Nama Jabatan</label>
		<input type="text" class="form-control" name="name"/>
		<br>
		<label>Kantor</label><br>
		<select name="kantor" class="form-control">
			<option value="pusat">Kantor Pusat</option>
			<option value="cabang">Kantor Cabang</option>
		</select>
		<br>
		<label>Jabatan Atasan</label>
			<select class="form-control select2" name="atasan" id="atasan">
			</select>
			<br>
			<br>
		<label>Tunjangan Program Pensiun</label>
		<input type="text" class="form-control" name="pensiun"/>
		<br>
		<label>Tunjangan Istri / Suami</label>
		<input type="text" class="form-control" name="tunis"/>
		<br>
		<label>Tunjangan Anak</label>
		<input type="text" class="form-control" name="tunak"/>
		<br>
		<label>Tunjangan Pangan</label>
		<input type="text" class="form-control" name="tunpang"/>
		<br>
		<label>Uang Makan</label>
		<input type="text" class="form-control" name="umak"/>
		<br>
		
		<input type="submit" class="btn btn-primary" value="save">
	</form>

@endsection
