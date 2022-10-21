@extends('layouts.global')
@section('title') Trashed Pangkat @endsection
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
				<a class="nav-link" href="{{route('pangkat.index')}}">Published</a>
			</li>
			<li class="nav-item">
					<a class="nav-link active" href="{{route('pangkat.trash')}}">Trash</a>
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
							<th>Nama Pangkat</th>
							<th>Gaji Pokok</th>
							<th>Action</th>
						</tr>
					</thead>
				<tbody>
					@foreach($pangkat as $pangkats)
					<tr>
						<td>{{$pangkats->name}}</td>
						<td>{{$pangkats->gapok}}</td>
						<td>
						<a href="{{route('pangkat.restore',[$pangkats->id])}}" class="btn btn-success">Restore</a>
						<form class="d-inline" action="{{route('pangkat.delete-permanent',[$pangkats->id])}}" method="POST" onsubmit="return confirm('Delete this Pangkat Permanently?')">
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
						{{$pangkat->appends(Request::all())->links()}}

					</td>
					</tr>
				</tfoot>
				</table>
			</div>
		</div>

@endsection