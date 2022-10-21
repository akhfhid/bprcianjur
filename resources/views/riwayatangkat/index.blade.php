@extends('layouts.global')
@section('title') Kepegawaian @endsection

@section('content')

<div class="row">

	<div class="col-md-6">
		<form action="{{route('riwayatangkat.list',[$pegawai['id']])}}">

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
		<a href="{{route('riwayatangkat.tambah',$pegawai['id'])}}" class="btn btn-primary">Create Data</a>
	</div>
</div>
		<b>Data Riwayat Kepegawaian {{$pegawai['name']}}</b><br>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">

					<th><b>Status Kepegawaian</b></th>
					<th><b>Tanggal Pengangkatan</b></th>
					<th><b>Nomor SK Pengangkatan</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
				@foreach ($dataangkat as $riwayatangkat)
				<td>{{$riwayatangkat['status']}}</td>
				<td>{{$riwayatangkat['tglangkat']}}</td>
				<td>{{$riwayatangkat['nosk']}}</td>

				<td><a href="{{route('riwayatangkat.edit',[$riwayatangkat['id']])}}" class="btn btn-info btn-sm"> Edit </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('riwayatangkat.delete-permanent',[$riwayatangkat['id']])}}">
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
