@extends('layouts.global')
@section('title')Data Trash Peraturan @endsection
@section ('content')

<div class="row">
	<div class="col-md-6">
		<form action="{{route('peraturan.trash')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Peraturan" value="{{Request::get('name')}}" name="name">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
	<div class="col-md-6">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link" href="{{route('peraturan.index')}}">Published</a>
			</li>
			<li class="nav item">
				<a class="nav-link active" href="{{route('peraturan.trash')}}">Trash</a>
			</li>
		</ul>
	</div>
</div>
<hr class="my-3">
<div class="row">
	
</div>
<br>
<div class="row">
	<div class="col-md-12">
		@if(session('status'))
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success">
					{{session('status')}}
				</div>
			</div>
		</div>
		@endif
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					<th><b>Nama Peraturan</b></th>
					<th><b>Nomor Surat Keputusan</b></th>
					<th><b>Tanggal Surat Keputusan</b></th>
					<th><b>Tanggal Masa berlaku</b></th>
					<th><b>Actions</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($peraturan as $atur)
				<tr>
					<td>{{$atur->name}}</td>
					<td>{{$atur->nosk}}</td>
					<td>{{$atur->tglsk}}</td>
					<td>{{$atur->tgllaku}}</td>
					<td>
						<a href="{{route('peraturan.showtrash',[$atur->id])}}" class="btn btn-primary btn-sm">Detail</a>
						@if(auth()->user()->roles == 'PATUH')
						<a href="{{route('peraturan.restore',[$atur->id])}}" class="btn btn-success btn-sm">Restore </a>
						<form class="d-inline" action="{{route('peraturan.delete-permanent',[$atur->id])}}" method="POST" onsubmit="return confirm('Delete this Peraturan Permanently?')">
							@csrf
							<input type="hidden" name="_method" value="DELETE">
							<input type="submit" class="btn btn-danger btn-sm" value="Delete">
						</form>
						@endif
					</td>
				</tr>
			</tbody>
			@endforeach
		</table>

</div>


@endsection