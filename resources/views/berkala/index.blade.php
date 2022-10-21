@extends('layouts.global')
@section('title') Pangkat Detail @endsection
@section('content')


	<hr class="my-3">
@if (session('status'))
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-success">
			{{session('status')}}
		</div>
	</div>
</div>
@endif

<div class="row">
	<div class="col-md-12 text-right">
		<a href="{{route('berkala.tambah',[$pangkat->id])}}" class="btn btn-primary">Create Golongan</a>
	</div>
</div>
<hr class="my-3">
<br>
<div class="row">
 	<div class="col-md-12">
 		<table class="table table-bordered table-stripped">
 			<thead>
 				<tr align="center">
 					<th><b>Pangkat/Golongan</b></th>
 					<th><b>Masa Kerja</b></th>
 					<th><b>Gaji Pokok</b></th>
 					<th><b>Actions</b></th>
 				</tr>
 			</thead>

 			<tbody>
 				@foreach ($databerkala as $berkala)
 				<tr align="center">
 					<td>{{$berkala['name']}}</td>
 					<td>{{$berkala['berkala']}} Tahun</td>
 					<td>{{$berkala['gapok']}}</td>
 					<td><a href="{{route('berkala.edit',[$berkala['id']])}}" class="btn btn-info btn-sm">Edit</a>
 						<form class="d-inline" action="{{route('berkala.destroy',[$berkala['id']])}}" method="POST" onsubmit="return confirm('Move Kantor To Trash?') ">
 							@csrf
 							<input type="hidden" name="_method" value="DELETE">
 							<input type="submit" class="btn btn-danger btn-sm" value="Trash">
 						</form>
 					</td>
 				</tr>
 				
 			</tbody>
 			@endforeach
 			<tfoot>
 				<tr>
 					<td colspan="10">
 						{{$berkalas->appends(Request::all())->links()}}
 					</td>
 				</tr>
 			</tfoot>
 			
 		</table>
 	</div>
 </div>

@endsection