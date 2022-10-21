@extends('layouts.global')
@section('title') List Data Riwayat Pekerjaan @endsection

@section('content')

<div class="row">

	<div class="col-md-6">
		<form action="{{route('pelatihan.list',[$pegawai['id']])}}">
			
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
		<a href="{{route('pelatihan.tambah',$pegawai['id'])}}" class="btn btn-primary">Create Data Pelatihan</a>
	</div>
</div>
		<b>Data Riwayat Pelatihan {{$pegawai['name']}}</b><br>
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					
					<th><b>Nama Pelatihan</b></th>
					<th><b>Penyelenggara Pelatihan</b></th>
					<th><b>Tanggal Pelatihan</b></th>
					<th><b>Sertifikat</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
				@foreach ($datapelatihan as $pelatihan)
				<td>{{$pelatihan->name}}</td>
				<td>{{$pelatihan->penyelenggara}}</td>
				<td>{{$pelatihan->thnlatih}}</td>
				<td>
					@if($pelatihan->image)
					<img src="{{asset('storage/'.$pelatihan->image)}}" width="70px">
					@else
					N/A
					@endif
				</td>
				
				<td><a href="{{route('pelatihan.edit',[$pelatihan['id']])}}" class="btn btn-info btn-sm"> Edit </a>
					<form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('pelatihan.destroy',[$pelatihan['id']])}}">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" value="Delete" class="btn btn-danger btn-sm">
						</form><br>
				</td>
			</tr>

				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						{{$datapelatihan->appends(Request::all())->links()}}
					</td>
				</tr>
			</tfoot>
		</table>
@endsection