@extends('layouts.global')
@section('title') List Data Keluarga @endsection

@section('content')



<hr class="my-3">
<div class="row">
	<div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif
 </div>

<div class="row">

	<div class="col-md-12">
		<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('gaji.tambah',$pegawai['id'])}}" class="btn btn-primary">Create Data Tunjangan</a>
	</div>
</div>
		<b>Data Tunjangan {{$pegawai['name']}}</b><br>

			<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					
					<th><b>Tunjangan Jabatan</b></th>
					<th><b>Tunjangan Fungsional</b></th>
					<th><b>Tunjangan BPJS Kesehatan</b></th>
					<th><b>Tunjangan BPJS Ketenagakerjaan</b></th>
					<th><b>Tunjangan PPH Pasal 21</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($gaji as $tunjangan)
				<tr>
					<td>{{$tunjangan['jabatan']}}</td>
					<td>{{$tunjangan['fungsi']}}</td>
					<td>{{$tunjangan['bpjsks']}}</td>
					<td>{{$tunjangan['bpjstk']}}</td>
					<td>{{$tunjangan['pph']}}</td>
					<td><a href="{{route('gaji.edit',[$tunjangan['id']])}}" class="btn btn-info btn-sm"> Edit </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('gaji.delete-permanent',[$tunjangan['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="Delete" class="btn btn-danger btn-sm">
						</form><br>
				</td>
				</tr>
				
			</tbody>
		</table>
		@endforeach
	</div>
</div>




@endsection