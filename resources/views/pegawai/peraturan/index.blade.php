@extends('layouts.global')
@section('title')Data Peraturan @endsection
@section ('content')

<div class="row">
	<div class="col-md-6">
		<form action="{{route('peraturan.index')}}">
			<div class="input-group">
				<input type="text" class="form-control" placeholder="Filter Nama Peraturan" value="{{Request::get('name')}}" name="name">
				<div class="input-group-append">
					<input type="submit" value="Filter" class="btn btn-primary"><br>
				</div>
			</div>

		</form>
	</div>
	<div class="col-md-6">
		<ul class="nav nav-pills card-header-pills">
			<li class="nav-item">
				<a class="nav-link active" href="{{route('peraturan.index')}}">Published</a>
			</li>
			<li class="nav item">
				<a class="nav-link" href="{{route('peraturan.trash')}}">Trash</a>
			</li>
			<li class="nav item">
				<a class="nav-link" href="{{route('peraturan.create')}}">Create Peraturan</a>
			</li>
				<a class="nav-link" href="{{route('kepatuhan.loguser')}}">Log Akses</a>
			
		</ul>

	</div>
</div>
	
<hr class="my-3">
<div class="row">           
 <div class="col-md-3">
                  <label>Peraturan</label>
                  <select id="filter-peraturan" class="form-control filter">
                    <option value="">Pilih Peraturan</option>
                    <option value="1">Internal</option>
                    <option value="2">POJK</option>
                    <option value="3">PBI</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Jenis Peraturan</label>
                  <select id="filter-jenis" class="form-control filter">
                    <option value="">Filter Jenis Peraturan</option>
                    <option value="1">SE</option>
                    <option value="0">SK</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Kategori Peraturan</label>
                  <select id="filter-kategori" class="form-control filter">
                    <option value="">Filter Kategori Peraturan</option>
                    <option value="1">Kredit</option>
                    <option value="2">Dana</option>
                    <option value="3">Umum</option>
                    <option value="4">TI</option>
                    <option value="5">Audit</option>
                    <option value="6">Kepatuhan</option>
                    <option value="7">Akunting</option>
                    <option value="8">APU-PPT</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Tahun</label>
                  <select id="filter-tahun" class="form-control filter">
                    <option value="0" selected> Pilih Tahun</option>
					<?php 
						$year = date('Y');
						$min = $year - 15;
		        		$max = $year;
						for( $i=$max; $i>=$min; $i-- ) {
						echo '<option value='.$i.'>'.$i.'</option>';
					}?>
                  </select>
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
						<a href="{{route('peraturan.edit',[$atur->id])}}" class="btn btn-info btn-sm"> Edit </a>
						<a href="{{route('peraturan.show',[$atur->id])}}" class="btn btn-primary btn-sm">Detail</a>
						<form class="d-inline" action="{{route('peraturan.destroy',[$atur->id])}}" method="POST" onsubmit="return confirm('move peraturan to trash?')">
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
						{{$peraturan->appends(Request::all())->links()}}
					</td>
				</tr>
			</tfoot>
		</table>

</div>


@endsection