@extends ('layouts.global')
@section('title') Edit Jabatan @endsection
@section('footer-scripts')

<link href="\node_modules\select2\dist\css\select2.min.css" rel="stylesheet" />
<script src="\node_modules\select2\dist\js\select2.min.js"></script>

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
			<option>Pilih Kantor</option>
			<option value="pusat">Kantor Pusat</option>
			<option> value="cabang">Kantor Cabang</option>
		</select>
		<br>
		<label>Jabatan Atasan</label>
			<select class="form-control select2" name="atasan" id="atasan">
			</select>
			<br>
			<br>
		<label>Tunjangan Program Pensiun</label>
		<input type="text" class="form-control" value="{{$jabatan->pensiun}}" name="tunjab">
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
