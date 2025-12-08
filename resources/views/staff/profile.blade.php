@extends ('layouts.global')
@section('title')Detail Data Pegawai @endsection

@section('content')

<div class="col-md-12">
<div class="card">
<div class="card-body">
<table border="0">
	<tr>
		<th rowspan="6">@if($pegawai->photo)
						<img src="{{asset('storage/'.$pegawai->photo)}}" width="180px">
						@endif
		&nbsp</th>
		<td><b>Nama <b></b></td><td>:</td><td>{{$pegawai->name}}</td><td>&nbsp</td>
		<td><b>Pendidikan<b></td><td>:</td><td>{{$pendidikan->name}}</td>

		
	</tr>
		<tr>
			<td><b>Tempat, Tanggal Lahir <b></td><td>:</td><td>{{$pegawai->templahir}},{{$pegawai->tgllahir}}</td><td>&nbsp</td>
				<td><b>Status Pegawai<b></td><td>:</td><td>{{$pegawai->spegawai}}</td>
		</tr>
		<tr>
			<td><b>Alamat <b></td><td>:</td><td>{{$pegawai->alamat}}</td><td>&nbsp</td>
				<td><b>Masa Kerja<b></td><td>:</td><td>{{$masakerja}} Tahun</td>
		</tr>
		<tr>
			<td><b>Agama <b></td><td>:</td><td>{{$agama->name}}</td><td>&nbsp</td>
			<td><b>Pangkat<b></td><td>:</td><td>{{$pangkat->name}}</td>

			
		</tr>
		<tr>
			<td><b>Status Pernikahan<b></td><td>:</td><td>{{$kawin->name}}</td><td>&nbsp</td>
				<td><b>Jabatan</b></td><td>:</td><td>{{$jabatan->name}}</td>
		</tr>
		<tr>
			<td><b>Umur<b> </td><td>:</td><td>{{$umur}} Tahun</td><td>&nbsp</td>
				<td><b>Kantor Cabang</b></td><td>:</td><td>{{$cabang->name}}</td>
		</tr>

</table> 
<hr class="my-3">
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
				<td>{{$keluargas['umurkel']}}</td>
				<td>{{$keluargas['alamat']}}</td>
				<td>{{$keluargas['hub']}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
<hr class="my-3">
<table class="table table-bordered table-stripped">
		<thead>
		<tr  align="center">
			<th colspan="5"><b>Data Pendidikan Pegawai {{$pegawai->name}}<b></th>
		</tr>
		<tr align="center">
					
					<th><b>Tingkat Pendidkan</b></th>
					<th><b>Nama Lembaga Pendidikan</b></th>
					<th><b>Gelar Pendidikan</b></th>
					<th><b>Tahun Lulus</b></th>
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
			<th colspan="5"><b>Data Pendidikan Karier {{$pegawai->name}}<b></th>
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
		<a href="{{route('pegawai.cetak',$pegawai->id)}}" class="btn btn-primary">Cetak Data Pegawai</a>

	</div>
</div>
</div>
</div>
@endsection