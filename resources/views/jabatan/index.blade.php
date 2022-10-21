@extends('layouts.global')
@section('title') Jabatan List @endsection
@section('content')


<div class="row">
	<div class="col-md-6">
		<form action="{{route('jabatan.index')}}">
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
				<a class="nav-link active" href="{{route('jabatan.index')}}">Published</a>
			</li>
			<li class="nav item">
				<a class="nav-link" href="{{route('jabatan.trash')}}">Trash</a>
			</li>
		</ul>
	</div>
</div>
<hr class="my-3">

<div class="row">
	<div class="col-md-12 text-right">
		<a href="{{route('jabatan.create')}}" class="btn btn-primary">Create Jabatan</a>
	</div>
</div>
<br>
<div class="row">
	<div class="col-md-12">
		@if(session('status'))
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-warning">
					{{session('status')}}
				</div>
			</div>
		</div>
		@endif
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					<th><b>Nama Jabatan</b></th>
					<th><b>Persentase Tunjangan Program Pensiun</b></th>
					<th><b>Persentase Tunjangan Istri / Suami</b></th>
					<th><b>Persentase Tunjangan Anak</b></th>
					<th><b>Tunjangan Pangan</b></th>
					<th><b>Uang Makan</b></th>

					<th><b>Actions</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($jabatan as $jabatans)
				<tr>
					<td>{{$jabatans->name}}</td>
					<td>{{$jabatans->pensiun}}</td>
					<td>{{$jabatans->tunis}}</td>
					<td>{{$jabatans->tunak}}</td>
					<td>{{$jabatans->tunpang}}</td>
					<td>{{$jabatans->umak}}</td>

					<td>
						<a href="{{route('jabatan.edit',[$jabatans->id])}}" class="btn btn-info btn-sm"> Edit </a>
						<form class="d-inline" action="{{route('jabatan.destroy',[$jabatans->id])}}" method="POST" onsubmit="return confirm('move jabatan to trash?')">
							@csrf
							<input type="hidden" value="DELETE" name="_method">
							<input type="submit" class="btn btn-danger btn-sm" value="Trash">
						</form>
					</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						{{$jabatan->appends(Request::all())->links()}}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>





@endsection
