@extends ('layouts.global')
@section('title')Detail Data Pegawai @endsection

@section('content')

<div class="col-md-12">
<div class="card">
<div class="card-body">
	<!--<font size="2">-->
<table border="0">
	<tbody>
		
	<tr>
		<th rowspan="30" class="align-top">@if($pegawai->photo)
						<img src="{{asset('storage/'.$pegawai->photo)}}" width="180px">
						@endif
		&nbsp</th>
		<td style="height: 10px; width: 200px;"><b>Nama <b></b></td><td>:</td><td>{{$pegawai->name}}</td>
	</tr>
	<tr>
		<td style="height: 10px;"><b>NIK Pegawai</b></td><td>:</td><td>{{$pegawai->nikpegawai}}</td>
	</tr>
	<tr><td style="height: 10px;"><b>NIK Kependudukan</b></td><td>:</td><td>{{$pegawai->nikpenduduk}}</td></tr>
		<tr>
			<td style="height: 10px;"><b>Tempat, Tanggal Lahir <b></td><td>:</td><td>{{$pegawai->templahir}},{{$pegawai->tgllahir}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Umur<b> </td><td>:</td><td>{{$umur}}</td>
		</tr>
		<tr>
			<td  style="height: 10px;"><b>Agama <b></td><td>:</td><td>{{$agama->name}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Golongan Darah</b></td><td>:</td><td>{{$pegawai->goldar}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Status Perkawinan<b></td><td>:</td><td>{{$kawin->name}}</td>
		</tr>
		<tr>
		<td style="height: 10px;"><b>Email<b></td><td>:</td><td>{{$pegawai->email}}</td>
		<tr>
			<td style="height: 10px;"><b>Alamat <b></td><td>:</td><td>{{$pegawai->alamat}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Pendidikan Terakhir<b></td><td>:</td><td>{{$pendidikan->name}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Status Kepegawaian<b></td><td>:</td><td>{{$pegawai->spegawai}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Tanggal Pengangkatan</b></td><td>:</td><td>{{$pegawai->tglmasuk}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Masa Kerja<b></td><td>:</td><td>{{$masakerja}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Sisa Masa Kerja<b></td><td>:</td><td>{{$smkerja}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Tanggal Pensiun</b></td><td>:</td><td>{{$pensiun}}</td>
		</tr>
		<tr>
			@if($pegawai->spegawai == 'Kontrak')
			<td style="height: 10px;"><b>Pangkat<b></td><td>:</td><td>-</td>
			@else
			<td style="height: 10px;"><b>Pangkat<b></td><td>:</td><td>{{$pangkat->name}}</td>
			@endif
			
		</tr>
		<tr>
			<td style="height: 10px;"><b>Jabatan</b></td><td>:</td><td>{{$jabatan->name}}</td>
		</tr>
		<tr>
			<td style="height: 10px;"><b>Kantor</b></td><td>:</td><td>{{$cabang->name}}</td>
		</tr>
</tbody>
</table> 
<table border="0">
	<tbody>
		
	</tbody>
</table>



<!--</font>-->
<hr class="my-3">
<div class="row mb-3">
	<div class="col-md-12 text-right">
<a href="{{route('keluarga.list',$pegawai->id)}}" class="btn btn-info btn-sm">Manage Data Keluarga </a><br>
</div>
</div>
	<table class="table table-bordered table-stripped">
		<thead>
		<tr  align="center">
			<th colspan="5"><b>Data Keluarga Pegawai {{$pegawai->name}}<b></th>
		</tr>
		<tr>
			<td><b>Nama</b></td>
			<td><b>Tempat, Tanggal Lahir</b></td>
			<td><b>Umur</b></td>
			<td><b>Alamat</b></td>
			<td><b>Hubungan</b></td>
		</tr>
		</thead>
		<tbody>
			@foreach ($keluarga as $keluargas)
			<tr>
				<td>{{$keluargas['name']}}</td>
				<td>{{$keluargas['templahir']}}, {{$keluargas['tgllahir']}}</td>
				<td>{{$keluargas['umurkel']}} Tahun</td>
				<td>{{$keluargas['alamat']}}</td>
				<td>{{$keluargas['hub']}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
<hr class="my-3">
<div class="row mb-3">
	<div class="col-md-12 text-right">
<a href="{{route('riwayatpendi.list',$pegawai->id)}}" class="btn btn-info btn-sm">Manage Data Pendidikan </a><br>
</div>
</div>
<table class="table table-bordered table-stripped">
		<thead>
		<tr  align="center">
			<th colspan="5"><b>Data Pendidikan Pegawai {{$pegawai->name}}<b></th>
		</tr>
		<tr align="center">
					
					<th><b>Tingkat Pendidikan</b></th>
					<th><b>Tahun</b></th>
					<th><b>Nama Lembaga Pendidikan</b></th>
					<th><b>Fakultas/Jurusan</b></th>
					<th><b>Gelar Pendidikan</b></th>
					
				</tr>
			</thead>
		<tbody>
				@foreach ($riwayatpendi as $riwayatpend)
				<td>{{$riwayatpend['pendidikan']}}</td>
					<td>{{$riwayatpend['thnlulus']}}</td>
				<td>{{$riwayatpend['name']}}</td>
				<td>{{$riwayatpend['jurusan']}}</td>	
				<td>{{$riwayatpend['gelar']}}</td>
				
			
		</tbody>
		@endforeach
	</table>

<hr class="my-3">
<div class="row mb-3">
	<div class="col-md-12 text-right">
<a href="{{route(')}}" class="btn btn-info btn-sm">Manage Riwayat </a><br>
</div>
</div>
<table class="table table-bordered table-stripped">
		<thead>
		<tr  align="center">
			<th colspan="5"><b>Riwayat Status Kepegawaian {{$pegawai->name}}<b></th>
		</tr>
		<tr align="center">
					
					<th><b>Status Kepegawaian</b></th>
					<th><b>Tanggal Pengangkatan</b></th>
					
					
				</tr>
			</thead>
		<tbody>
				
			
		</tbody>
		
	</table>

<hr class="my-3">
<div class="row mb-3">
	<div class="col-md-12 text-right">
<a href="{{route('riwayatkerja.list',$pegawai->id)}}" class="btn btn-info btn-sm">Manage Data Karier </a><br>
</div>
</div>
<table class="table table-bordered table-stripped">
			<thead>
				<tr  align="center">
			<th colspan="5"><b>Data Karier {{$pegawai->name}}<b></th>
		</tr>
		<tr align="center">
					
					<th><b>Jabatan</b></th>
					<th><b>Kantor Cabang</b></th>
					<th><b>Tahun Pengangkatan</b></th>
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
			<th colspan="7"><b>Data Penghasilan {{$pegawai->name}}<b></th>
		</tr>
		<tr align="center">
					
					<th><b>Gaji Pokok</b></th>
					<th><b>Tunjangan Kemahalan</b></th>
					<th><b>Tunjangan Jabatan</b></th>
					<th><b>Tunjangan Istri</b></th>
					<th><b>Tunjangan Anak</b></th>
					<td><b>Tunjangan Pangan</b></td>
					<th><b>Total</b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
				@if($pegawai->spegawai == 'Kontrak')
				<td>Rp. 1.850.000</td>
				@else
				<td>@currency($pangkat->gapok)</td>
				@endif
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
		<div class="row mb-3">
	<div class="col-md-12 text-right">
<a href="{{route('pelatihan.list',$pegawai->id)}}" class="btn btn-info btn-sm">Manage Data Pelatihan </a><br>
</div>
</div>
<table class="table table-bordered table-stripped">
			<thead>
				<tr  align="center">
			<th colspan="7"><b>Data Pelatihan {{$pegawai->name}}<b></th>
		</tr>
		<tr align="center">
					
					<th><b>Nama Pelatihan</b></th>
					<th><b>Penyelenggara Pelatihan</b></th>
					<th><b>Tahun Pelatihan</b></th>
					<th><b>Sertifikat</b></th>
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
			<div class="col-md-12 text-right">
		

	</div>
</div>
</div>
</div>
@endsection