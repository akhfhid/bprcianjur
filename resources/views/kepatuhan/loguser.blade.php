@extends('layouts.global')
@section('title') List Log Pegawai @endsection
@section('content')
	
<div class="row">
	<div class="col-md-6">
		<form action="{{route('kepatuhan.loguser')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter by Nama Pegawai" 
						value="{{Request::get('keyword')}}" name="keyword">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
</div>
<hr class="my-3">
<div class="row">
	<div class="col-md-12 text-right">
		<a href="{{route('peraturan.index')}}" class="btn btn-primary">Kembali</a>
		
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
				<tr>
					<th><b>Nama Pegawai</b></th>
					<th><b>Jenis</b></th>
					<th><b>Keterangan</b></th>
					<th><b>Waktu Akses</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach($loguser as $log)
				<tr>
					<td>{{$log->nampeg}}</td>
					<td>{{$log->jenis}}</td>
					<td>{{$log->keterangan}}</td>
					<td>{{$log->created_at}}</td>
					
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						{{$loguser->appends(Request::all())->links()}}
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
</div>



@endsection