@extends('layouts.global')
@section('title') Trashed Jabatan @endsection
@section('content')

<div class="row">
	<div class="col-md-6">
		<form action="{{route('jabatan.trash')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Jabatan" value="{{Request::get('name')}}" name="name">
			<div class="input-group-append">
				<input type="submit" value="Filter" class="btn btn-primary">
			</div>
		</div>
	</form>
</div>
<div class="col-md-6">
	<ul class="nav nav-pills card-header-pills">
		<li class="nav-item">
			<a class="nav-link" href="{{route('jabatan.index')}}">Published</a>
		</li>
		<li class="nav-item">
			<a class="nav-link active" href="{{route('jabatan.trash')}}">Trash</a>
		</li>
	</ul>
</div>
</div>
<hr class="my-3">
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th><b>Nama Jabatan</b></th>
					<th><b>Tunjangan Jabatan</b></th>
					<th><b>Tunjangan Istri</b></th>
					<th><b>Tunjangan Anak</b></th>
					<th><b>Tunjangan Pangan</b></th>
					<th><b>Uang Makan Jabatan</b></th>
					<th><b>Actions</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach($jabatan as $jabatans)
				<tr>
					<td>{{$jabatans->name}}</td>
					<td>{{$jabatans->tunjab}}</td>
					<td>{{$jabatans->tunis}}</td>
					<td>{{$jabatans->tunak}}</td>
					<td>{{$jabatans->tunpang}}</td>
					<td>{{$jabatans->umak}}</td>
					<td>
						<a href="{{route('jabatan.restore',[$jabatans->id])}}" class="btn btn-success">Restore </a>
						<form class="d-inline" action="{{route('jabatan.delete-permanent',[$jabatans->id])}}" method="POST" onsubmit="return confirm('Delete this Jabatan Permanently?')">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" class="btn btn-danger btn-sm" value="Delete">
						</form>
					</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						{{$jabatan->appends(Request::all())->Links()}}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
@endsection