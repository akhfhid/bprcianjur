@extends('layouts.global')
@section('title') List Data Riwayat Pekerjaan @endsection

@section('content')

<div class="row">

	<div class="col-md-6">
		<form action="{{route('riwayatkerja.list',[$pegawai['id']])}}">

		</form>
	</div>
</div>
	<hr class="my-3">
<div class="row">
	<div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>
@endif

<div class="row mb-3">

	<div class="col-md-12 text-right">
		<a href="{{route('riwayatkerja.tambah',$pegawai['id'])}}" class="btn btn-primary">Create Data Karier</a>
	</div>
</div>
		<b>Data Riwayat Pekerjaan {{$pegawai['name']}}</b><br>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">

					<th><b>Jabatan</b></th>
					<th><b>Kantor</b></th>
					<th><b>Periode</b></th>
					<th><b>Lama Masa Jabatan</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
				@foreach ($data as $riwayatkerja)
				<td>{{$riwayatkerja['name']}}</td>
				<td>{{$riwayatkerja['kantor']}}</td>
				<td>{{$riwayatkerja['tglawal']}} - 
			
				{{$riwayatkerja['tglakhir']}}
			
			</td>
				<td>{{$riwayatkerja['periode']}}</td>

				<td><a href="{{route('riwayatkerja.edit',[$riwayatkerja['id']])}}" class="btn btn-info btn-sm"> Edit </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('riwayatkerja.delete-permanent',[$riwayatkerja['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="Delete" class="btn btn-danger btn-sm">
						</form><br>
				</td>
			</tr>


				@endforeach
			</tbody>
		</table>
@endsection
