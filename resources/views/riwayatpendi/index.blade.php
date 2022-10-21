@extends('layouts.global')
@section('title') List Data Riwayat Pendidikan @endsection

@section('content')

<div class="row">

	<div class="col-md-6">
		<form action="{{route('riwayatpendi.list',[$pegawai['id']])}}">
			
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
		<a href="{{route('riwayatpendi.tambah',$pegawai['id'])}}" class="btn btn-primary">Create Data Pendidikan</a>
	</div>
</div>
		<b>Data Riwayat Pendidikan {{$pegawai['name']}}</b><br>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					
					<th><b>Tingkat Pendidkan</b></th>
					<th><b>Tahun</b></th>
					<th><b>Nama Lembaga Pendidikan</b></th>
					<th><b>Fakultas/Jurusan</b></th>
					<th><b>Gelar Pendidikan</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($datariwayat as $riwayatpendi)
				<td>{{$riwayatpendi->pendidikan}}</td>
				<td>{{$riwayatpendi->thnlulus}}</td>
				<td>{{$riwayatpendi->name}}</td>
				<td>{{$riwayatpendi->jurusan}}</td>	 
				<td>{{$riwayatpendi->gelar}}</td>
				
				<td><a href="{{route('riwayatpendi.edit',[$riwayatpendi['id']])}}" class="btn btn-info btn-sm"> Edit </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('riwayatpendi.delete-permanent',[$riwayatpendi['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="Delete" class="btn btn-danger btn-sm">
						</form><br>
				</td>
			</tbody>
			@endforeach
		</table>
	</div>
</div>

@endsection