@extends('layouts.global')
@section('title')List Permohonan Cuti @endsection
@section('content')

<div class="row">
	<div class="col-md-12">
		<table class="table table-stripped table-bordered">
			<thead>
				<tr align="center">
					
					<th><b>Nama Pegawai</b></th>
					<th><b>Kantor Cabang</b></th>
					<th><b>Jumlah Cuti</b></th>
					<th><b>Tanggal Awal Cuti</b></th>
					<th><b>Tanggal Akhir Cuti</b></th>
					<th><b>Alasan Cuti</b></th>
					<th><b>Status</b></th>
					<th><b>Action</b></th>
				</tr>
			</thead>
			<tr>
				<td colspan="8">Data Tidak Ditemukan</td>
			</tr>
			<tbody>
			</tbody>
		</table>
	</div>
</div>


@endsection