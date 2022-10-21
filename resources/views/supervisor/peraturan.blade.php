@extends('layouts.global')
@section('title')Data Peraturan @endsection
@section ('content')

<div class="row">
	<div class="col-md-6">
		<form action="{{route('supervisor.peraturan')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Peraturan" value="{{Request::get('name')}}" name="name">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
</div>
<hr class="my-3">

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
	<div class="row mb-3">
	<!--<div class="col-md-12 text-right">
		<a href="{{route('supervisor.status')}}" class="btn btn-primary">Status Permintaan Data</a>
		
	</div>
</div>-->
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
						<a href="{{route('supervisor.showatur',$atur['id'])}}" class="btn btn-primary">Detail</a>
						
					</td>
				</tr>
			
			@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						{{$peraturan->appends(Request::all())->links()}}
					</td>
				</tr>
			</tfoot>
		</table>

</div>

   
@endsection