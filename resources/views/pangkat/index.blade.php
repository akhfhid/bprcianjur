@extends('layouts.global')
@section('title') Pangkat List @endsection
@section('content')

<div class="row">

	<div class="col-md-6">
		<form action="{{route('pangkat.index')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Pangkat name" value="{{Request::get('name')}}" name="name">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
	<div class="col-md-6">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link active" href="{{route('pangkat.index')}}">Published</a>
			</li>
			<li class="nav-item">
			<a class="nav-link" href="{{route('pangkat.trash')}}">Trash</a>
		</li>
		</ul>
	</div>
</div>
<hr class="my-3">
@if(session('status'))
	<div class="alert alert-success">
		{{session('status')}}
	</div>
	@endif

	<div class="row">
		<div class="col-md-12 text-right">
			<a href="{{route('pangkat.create')}}" class="btn btn-primary">Create Pangkat</a>
		</div>
	</div>
	<br>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-stripped">
			<thead>
				<tr align="center">
					<th align="center"><b>Pangkat</b></th>
					<th align="center"><b>Actions</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($pangkat as $pangkats)
				<tr>
					<td>{{$pangkats->name}}</td>
					<td>
					<a href="{{route('pangkat.edit',[$pangkats->id])}}" class="btn btn-info btn-sm">Edit</a>
					<a href="{{route('berkala.list',[$pangkats->id])}}" class="btn btn-success btn-sm">Detail </a>
					<form class="d-inline" action="{{route('pangkat.destroy',[$pangkats->id])}}" method="POST" onsubmit="return confirm('Move Pangkat To Trash')">
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
						{{$pangkat->appends(Request::all())->links()}}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>


@endsection