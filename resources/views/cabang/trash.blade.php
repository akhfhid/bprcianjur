@extends('layouts.global')
@section('title') Trashed Cabang @endsection
@section('content')



<div class="row">
	<div class="col-md-6">
		<form action="{{route('cabang.index')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Cabang" value="{{Request::get('name')}}" name="name">

				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
		</div>
		<div class="col-md-6">
			<ul class="nav nav-pills card-header-pills">
				<li class="nav-item">
					<a class="nav-link" href="{{route('cabang.index')}}"> Published </a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" href="{{route('cabang.trash')}}"> Trash </a>
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
					<th><b>Nama Kantor</b></th>
 					<th><b>Klasifikasi Kantor</b></th>
 					<th><b>Persentase Tunjangan Kinerja</b></th>
 					<th><b>Actions</b></th>
					</tr>
				</thead>
				<tbody>
					@foreach($cabang as $kantor)
					<tr>
						<td>{{$kantor->name}}</td>
 						<td>{{$kantor->class}}</td>
 						<td>{{$kantor->tunjangan}}</td>
						<td><a href="{{route('cabang.restore',[$kantor->id])}}" class="btn btn-success">Restore</a>
							<form class="d-inline" 
							action="{{route('cabang.delete-permanent',[$kantor->id])}}"
								method="POST" onsubmit="return confirm('Delete This Cabang Permanently?')">
								@csrf
								<input type="hidden" name="_method" value="DELETE"/>
								<input type="submit" class="btn btn-danger btn-sm" value="DELETE"/>
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