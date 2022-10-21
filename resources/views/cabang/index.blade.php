@extends('layouts.global')
@section('title') List Kantor @endsection
@section('content')


<div class="row">
	<div class="col-md-6">
		<form action="{{route('cabang.index')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter Nama Kantor" name="name">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
	<div class="col-md-6">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link active" href="{{route('cabang.index')}}">Published</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="{{route('cabang.trash')}}">Trash</a>
			</li>
		</ul>
	</div>
</div>
	<hr class="my-3">
@if (session('status'))
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-warning">
			{{session('status')}}
		</div>
	</div>
</div>
@endif

<div class="row">
	<div class="col-md-12 text-right">
		<a href="{{route('cabang.create')}}" class="btn btn-primary">Create Kantor</a>
	</div>
</div>
<br>
 <div class="row">
 	<div class="col-md-12">
 		<table class="table table-bordered table-stripped">
 			<thead>
 				<tr align="center">
 					<th><b>Nama Kantor</b></th>
 					<th><b>Klasifikasi Kantor</b></th>
 					<th><b>Persentase Tunjangan Kinerja</b></th>
 					<th><b>Actions</b></th>
 				</tr>
 			</thead>

 			<tbody>
 				@foreach ($cabang as $kantor)
 				<tr>
 					<td>{{$kantor->name}}</td>
 					<td>{{$kantor->class}}</td>
 					<td>{{$kantor->tunjangan}}</td>
 					<td><a href="{{route('cabang.edit',[$kantor->id])}}" class="btn btn-info btn-sm">Edit</a>
 						<form class="d-inline" action="{{route('cabang.destroy',[$kantor->id])}}" method="POST" onsubmit="return confirm('Move Kantor To Trash?') ">
 							@csrf
 							<input type="hidden" name="_method" value="DELETE">
 							<input type="submit" class="btn btn-danger btn-sm" value="Trash">
 						</form>
 					</td>

 				</tr>
 				@endforeach
 			</tbody>
 			<tfoot>
 				<tr>
 					<td colspan="10">
 						{{$cabang->appends(Request::all())->links()}}
 					</td>
 				</tr>
 			</tfoot>
 		</table>
 	</div>
 </div>
@endsection