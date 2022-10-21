<?php
header('Access-Control-Allow-Origin: localhost');
?>  
@extends('layouts.app')
@section('title') Show Data pegawai @endsection

@section('content')

<div class="col-md-12">
<div class="card">
<div class="card-body">
<table border="0">
	<tr>
		<th rowspan="6">@if($pegawai->photo)
						<img src="{{asset('storage/'.$pegawai->photo)}}" width="180px">
						@endif
		</th>
		<td>Nama</td><td>:</td><td>{{$pegawai->name}}</td><td></td>
		<td>Pendidikan</td><td>:</td><td>{{$pendidikan->name}}</td>

		
	</tr>
		<tr>
			<td>Tempat, Tanggal Lahir</td><td>:</td><td>{{$pegawai->templahir}},{{$pegawai->tgllahir}}</td><td></td>
				<td>Status Pegawai</td><td>:</td><td>{{$pegawai->spegawai}}</td>
		</tr>
		<tr>
			<td>Alamat </td><td>:</td><td>{{$pegawai->alamat}}</td><td></td>
				<td>Masa Kerja</td><td>:</td><td>{{$masakerja}} Tahun</td>
		</tr>
		<tr>
			<td>Agama </td><td>:</td><td>{{$agama->name}}</td><td></td>
			<td>Pangkat</td><td>:</td><td>{{$pangkat->name}}</td>

			
		</tr>
		<tr>
			<td>Status Pernikahan</td><td>:</td><td>{{$kawin->name}}</td><td></td>
				<td>Jabatan</b></td><td>:</td><td>{{$jabatan->name}}</td>
		</tr>
		<tr>
			<td>Umur </td><td>:</td><td>{{$umur}} Tahun</td><td></td>
				<td>Kantor Cabang</b></td><td>:</td><td>{{$cabang->name}}</td>
		</tr>

</table> 
<hr class="my-3">

	<table class="table table-bordered table-stripped">
		<thead>
		<tr  align="center">
			<th colspan="5">Data Keluarga Pegawai {{$pegawai->name}}</th>
		</tr>
		<tr>
			<td>Nama</b></td>
			<td>Tempat, Tanggal Lahir</b></td>
			<td>Umur</b></td>
			<td>Alamat</b></td>
			<td>Hubungan</b></td>
		</tr>
		</thead>
		<tbody>
			@foreach ($keluarga as $keluargas)
			<tr>
				<td>{{$keluargas['name']}}</td>
				<td>{{$keluargas['templahir']}}, {{$keluargas['tgllahir']}}</td>
				<td>{{$umurkel}}</td>
				<td>{{$keluargas['alamat']}}</td>
				<td>{{$keluargas['hubungan']}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
<hr class="my-3">
<table class="table table-bordered table-stripped">
		<thead>
		<tr  align="center">
			<th colspan="5">Data Pendidikan Pegawai {{$pegawai->name}}</th>
		</tr>
		<tr align="center">
					
					<th>Tingkat Pendidkan</b></th>
					<th>Nama Lembaga Pendidikan</b></th>
					<th>Gelar Pendidikan</b></th>
					<th>Tahun Lulus</b></th>
				</tr>
			</thead>
		<tbody>
				@foreach ($riwayatpendi as $riwayatpend)
				<td>{{$riwayatpend['pendidikan']}}</td>
				<td>{{$riwayatpend['name']}}</td>
				<td>{{$riwayatpend['gelar']}}</td>
				<td>{{$riwayatpend['thnlulus']}}</td>
			@endforeach
		</tbody>
	</table>
<hr class="my-3">
<table class="table table-bordered table-stripped">
			<thead>
				<tr  align="center">
			<th colspan="5">Data Pendidikan Karier {{$pegawai->name}}</th>
		</tr>
		<tr align="center">
					
					<th>Jabatan</b></th>
					<th>Kantor Cabang</b></th>
					<th>Tahun Pengangkatan</b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
				@foreach ($riwayatkerja as $riwayatkerjas)
				<td>{{$riwayatkerjas['name']}}</td>
				<td>{{$riwayatkerjas['kantorcabang']}}</td>
				<td>{{$riwayatkerjas['thnangkat']}}</td>
			</tr>
			@endforeach
			</tbody>
		</table>
<hr class="my-3">
<table class="table table-bordered table-stripped">
			<thead>
				<tr  align="center">
			<th colspan="7">Data Penghasilan {{$pegawai->name}}</th>
		</tr>
		<tr align="center">
					
					<th>Gaji Pokok</b></th>
					<th>Tunjangan Kemahalan</b></th>
					<th>Tunjangan Jabatan</b></th>
					<th>Tunjangan Istri</b></th>
					<th>Tunjangan Anak</b></th>
					<td>Tunjangan Pangan</b></td>
					<th>Total</b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
				<td>@currency($pangkat->gapok)</td>
				<td>@currency($tuncabang)</td>
				<td>@currency($jabatan->tunjab)</td>
				<td>@currency($tunjanganistri)</td>
				<td>@currency($tunjangananak)</td>
				<td>@currency($jabatan->tunpang)</td>
				<td>@currency($total)</td>
			</tr>
			</tbody>
		</table>
		<hr class="my-3">
<table class="table table-bordered table-stripped">
			<thead>
				<tr  align="center">
			<th colspan="7">Data Pelatihan {{$pegawai->name}}</th>
		</tr>
		<tr align="center">
					
					<th>Nama Pelatihan</b></th>
					<th>Penyelenggara Pelatihan</b></th>
					<th>Tahun Pelatihan</b></th>
					<th>Sertifikat</b></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($pelatihan as $latih)
				<tr>
				<td>{{$latih->name}}</td>
				<td>{{$latih->penyelenggara}}</td>
				<td>{{$latih->thnlatih}}</td>
				<td>
					@if($latih->image)
					<img src="{{asset('storage/'.$latih->image)}}" width="70px">
					@else
					N/A
					@endif
				</td>
			</tr>
			@endforeach
			</tbody>
		</table>

</div>
</div>
</div>
