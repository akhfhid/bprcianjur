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
                        <td style="height: 10px;"><b>NPWP<b></td><td>:</td><td>{{$pegawai->npwp}}</td>
                    <tr>
                    <tr>
                        <td style="height: 10px;"><b>No. HP<b></td><td>:</td><td>{{$pegawai->nohp}}</td>
                    <tr>
                    <tr>
                        <td style="height: 10px;"><b>Email<b></td><td>:</td><td>{{$pegawai->email}}</td>
                    <tr>
                        <td style="height: 10px;"><b>Alamat <b></td><td>:</td><td>{{$pegawai->alamat}}</td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"><b>Pendidikan Terakhir<b></td><td>:</td><td>{{$pendidikan->name}}</td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"><b>Status Kepegawaian<b></td><td>:</td><td>{{$spegawai->name}}</td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"><b>TMT</b></td><td>:</td><td>{{$pegawai->tglangkat}}</td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"><b>Masa Kerja<b></td><td>:</td><td>{{$masakerja}}</td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"><b>Sisa Masa Kerja<b></td><td>:</td><td>{{$smkerja}}</td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"><b>Tanggal Pensiun</b></td><td>:</td><td>{{$ppensiun}}</td>
                    </tr>
                    <tr>
                        @if($pegawai->spegawai == 'Kontrak')
                            <td style="height: 10px;"><b>Pangkat<b></td><td>:</td><td>-</td>
                        @else
                            <td style="height: 10px;"><b>Pangkat<b></td><td>:</td><td>{{$pangkat->name}} / {{$pegawai->mkpang}} Tahun</td>
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

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="5"><b>Data Keluarga Pegawai<b></th>
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

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="5"><b>Data Pendidikan Pegawai<b></th>
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

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="5"><b>Riwayat Status Kepegawaian<b></th>
                    </tr>
                    <tr align="center">

                        <th><b>Status Kepegawaian</b></th>
                        <th><b>Tanggal Pengangkatan</b></th>
                        <th><b>Nomor SK Pengangkatan</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach ($dataangkat as $riwayatangkat)
                            <td>{{$riwayatangkat['status']}}</td>
                            <td>{{$riwayatangkat['tglangkat']}}</td>
                            <td>{{$riwayatangkat['nosk']}}</td>
                    </tr>
                    </tbody>
                    @endforeach
                </table>

                <hr class="my-3">

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="5"><b>Data Karier<b></th>
                    </tr>
                    <tr align="center">

                        <th><b>Jabatan</b></th>
                        <th><b>Kantor</b></th>
                        <th><b>Periode Jabatan</b></th>
                        <th><b>Lama Masa Jabatan</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach ($riwayatkerja as $riwayatkerjas)
                            <td>{{$riwayatkerjas['name']}}</td>
                            <td>{{$riwayatkerjas['kantorcabang']}}</td>
                            <td>{{$riwayatkerjas['tglawal']}} - {{$riwayatkerjas['tglakhir']}}</td>
                            <td>{{$riwayatkerjas['periode']}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                <hr class="my-3">

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="5"><b>Sanksi Tertulis<b></th>
                    </tr>
                    <tr align="center">

                        <th><b>Jenis Sanksi</b></th>
                        <th><b>Tanggal Sanksi</b></th>
                        <th><b>Nomor Sanksi</b></th>
                        <th><b>Keterangan</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($datasanksi as $riwayatsanksi)
                        <td>{{$riwayatsanksi['sanksipeg']}}</td>
                        <td>{{$riwayatsanksi['tglsanksi']}}</td>
                        <td>{{$riwayatsanksi['nosanksi']}}</td>
                        <td>{{$riwayatsanksi['ket']}}</td>
                        <tr>

                        </tr>

                    </tbody>
                    @endforeach
                </table>

                <hr class="my-3">

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="5"><b>Data Kenaikan Gaji Pokok Pegawai<b></th>
                    </tr>
                    <tr align="center">

                        <th><b>Tanggal Kenaikan Gaji Berkala Terakhir</b></th>
                        <th><b>Tanggal Kenaikan Pangkat Terakhir</b></th>
                        <th><b>Lama Penundaan</b></th>
                        <th><b>Jadwal Kenaikan Gaji Berkala</b></th>
                        <th><b>Jadwal Kenaikan Pangkat</b></th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr align="center">
                        <td>{{$tglberkala}}</td>
                        <td>{{$tglpangkat}}</td>
                        <td>{{$tunda}} Bulan</td>
                        <td>{{$jdber}}</td>
                        <td>{{$jdpang}}</td>
                    </tr>

                    </tbody>

                </table>

                <hr class="my-3">

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="3"><b>Data Penghasilan<b></th>
                    </tr>
                    <tr style="height : 10px">

                        <td><b>Gaji Pokok</b></td>
                        <td> : </td>
                        <td align="right">@currency($gapokpeg) </td>
                    </tr>

                    <tr>
                        <th><b>Tunjangan Istri / Suami</b></th>
                        <td> : </td>
                        <td align="right">@currency($tunjanganistri) </td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan Anak</b></th>
                        <td> : </td>
                        <td align="right">@currency($tunjangananak) </td>
                    </tr>
                    <tr>
                        <td><b>Tunjangan Pangan</b></td>
                        <td> : </td>
                        <td align="right">@currency($pangan) </td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan Kinerja</b></th>
                        <td> : </td>
                        <td align="right">@currency($tuncabang)</td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan BPJS Tenaga Kerja</b></th>
                        <td> : </td>
                        <td align="right">@currency($bpjstk)</td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan BPJS Kesehatan</b></th>
                        <td> : </td>
                        <td align="right">@currency($bpjsks)</td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan Program Pensiun</b></th>
                        <td> : </td>
                        <td align="right">@currency($tunpen)</td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan PPH Pasal 21</b></th>
                        <td> : </td>
                        <td align="right">@currency($pph)</td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan Jabatan</b></th>
                        <td> : </td>
                        <td align="right">@currency($tunjab)</td>
                    </tr>
                    <tr>
                        <th><b>Tunjangan Fungsional</b></th>
                        <td> : </td>
                        <td align="right">@currency($fungsi)</td>
                    </tr>


                    <tr>
                        <th><b>Total Penghasilan</b></th>
                        <td><b> : </b></td>
                        <td align="right"><b>@currency($total)</b></td>
                    </tr>

                </table>

                <hr class="my-3">

                <table class="table table-bordered table-stripped">
                    <thead>
                    <tr  align="center">
                        <th colspan="7"><b>Data Pelatihan<b></th>
                    </tr>
                    <tr align="center">

                        <th><b>Nama Pelatihan</b></th>
                        <th><b>Penyelenggara Pelatihan</b></th>
                        <th><b>Tanggal Pelatihan</b></th>
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
