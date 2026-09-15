@extends('layouts.global')@section('title', 'Profile Pegawai')

@section('content')
    <div class="row">
        <!-- Kolom Kiri: Foto & Info Singkat -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    @if($pegawai->photo)
                        <img src="{{ asset('storage/' . $pegawai->photo) }}" class="rounded-circle img-thumbnail mb-3" width="180" alt="Foto Pegawai">
                    @else
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white mb-3" style="width: 180px; height: 180px; font-size: 4rem;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <h4 class="mb-1 font-weight-bold">{{ $pegawai->name }}</h4>
                    <p class="text-muted mb-2">{{ $jabatan->name }} - {{ $cabang->name }}</p>
                    <span class="badge badge-primary px-3 py-2">{{ $spegawai->name }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Data Pribadi -->
        <div class="col-md-8 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white font-weight-bold">
                    Informasi Pribadi
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr><th width="35%">NIK Pegawai</th><td width="2%">:</td><td>{{ $pegawai->nikpegawai }}</td></tr>
                                <tr><th>NIK Kependudukan</th><td>:</td><td>{{ $pegawai->nikpenduduk }}</td></tr>
                                <tr><th>Tempat, Tanggal Lahir</th><td>:</td><td>{{ $pegawai->templahir }}, {{ \Carbon\Carbon::parse($pegawai->tgllahir)->format('d-m-Y') }}</td></tr>
                                <tr><th>Umur</th><td>:</td><td>{{ $umur }}</td></tr>
                                <tr><th>Agama</th><td>:</td><td>{{ $agama->name }}</td></tr>
                                <tr><th>Golongan Darah</th><td>:</td><td>{{ $pegawai->goldar }}</td></tr>
                                <tr><th>Status Perkawinan</th><td>:</td><td>{{ $kawin->name }}</td></tr>
                                <tr><th>NPWP</th><td>:</td><td>{{ $pegawai->npwp }}</td></tr>
                                <tr><th>No. HP</th><td>:</td><td>{{ $pegawai->nohp }}</td></tr>
                                <tr><th>Email</th><td>:</td><td>{{ $pegawai->email }}</td></tr>
                                <tr><th>Alamat</th><td>:</td><td>{{ $pegawai->alamat }}</td></tr>
                                <tr><th>Pendidikan Terakhir</th><td>:</td><td>{{ $pendidikan->name }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <!-- Data Kepegawaian -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Detail Kepegawaian</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><th width="40%">Status Kepegawaian</th><td width="2%">:</td><td>{{ $spegawai->name }}</td></tr>
                                <tr><th>TMT</th><td>:</td><td>{{ $pegawai->tglangkat }}</td></tr>
                                <tr><th>Masa Kerja</th><td>:</td><td>{{ $masakerja }}</td></tr>
                                <tr><th>Sisa Masa Kerja</th><td>:</td><td>{{ $smkerja }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr><th width="40%">Tanggal Pensiun</th><td width="2%">:</td><td>{{ $ppensiun }}</td></tr>
                                <tr>
                                    <th>Pangkat</th><td>:</td>
                                    <td>{{ $pegawai->spegawai == 'Kontrak' ? '-' : $pangkat->name . ' / ' . $pegawai->mkpang . ' Tahun' }}</td>
                                </tr>
                                <tr><th>Jabatan</th><td>:</td><td>{{ $jabatan->name }}</td></tr>
                                <tr><th>Kantor</th><td>:</td><td>{{ $cabang->name }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Keluarga -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Data Keluarga Pegawai</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Tempat, Tanggal Lahir</th>
                                    <th>Umur</th>
                                    <th>Alamat</th>
                                    <th>Hubungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($keluarga as $keluargas)
                                    <tr>
                                        <td>{{ $keluargas['name'] }}</td>
                                        <td>{{ $keluargas['templahir'] }}, {{ \Carbon\Carbon::parse($keluargas['tgllahir'])->format('d-m-Y') }}</td>
                                        <td>{{ $keluargas['umurkel'] }} Tahun</td>
                                        <td>{{ $keluargas['alamat'] }}</td>
                                        <td>{{ $keluargas['hub'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada data keluarga</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pendidikan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Data Pendidikan Pegawai</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tingkat Pendidikan</th>
                                    <th>Tahun</th>
                                    <th>Nama Lembaga Pendidikan</th>
                                    <th>Fakultas/Jurusan</th>
                                    <th>Gelar Pendidikan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatpendi as $riwayatpend)
                                    <tr>
                                        <td>{{ $riwayatpend['pendidikan'] }}</td>
                                        <td>{{ $riwayatpend['thnlulus'] }}</td>
                                        <td>{{ $riwayatpend['name'] }}</td>
                                        <td>{{ $riwayatpend['jurusan'] }}</td>
                                        <td>{{ $riwayatpend['gelar'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada data pendidikan</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Riwayat Kepegawaian & Karier -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white font-weight-bold">Riwayat Status Kepegawaian</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Status</th>
                                            <th>Tgl Pengangkatan</th>
                                            <th>No. SK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dataangkat as $riwayatangkat)
                                            <tr>
                                                <td>{{ $riwayatangkat['status'] }}</td>
                                                <td>{{ $riwayatangkat['tglangkat'] }}</td>
                                                <td>{{ $riwayatangkat['nosk'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center">Tidak ada data</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white font-weight-bold">Sanksi Tertulis</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Jenis Sanksi</th>
                                            <th>Tgl Sanksi</th>
                                            <th>No. Sanksi</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($datasanksi as $riwayatsanksi)
                                            <tr>
                                                <td>{{ $riwayatsanksi['sanksipeg'] }}</td>
                                                <td>{{ $riwayatsanksi['tglsanksi'] }}</td>
                                                <td>{{ $riwayatsanksi['nosanksi'] }}</td>
                                                <td>{{ $riwayatsanksi['ket'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center">Tidak ada data sanksi</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Karier -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Data Karier</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Jabatan</th>
                                    <th>Kantor</th>
                                    <th>Periode Jabatan</th>
                                    <th>Lama Masa Jabatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatkerja as $riwayatkerjas)
                                    <tr>
                                        <td>{{ $riwayatkerjas['name'] }}</td>
                                        <td>{{ $riwayatkerjas['kantorcabang'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($riwayatkerjas['tglawal'])->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($riwayatkerjas['tglakhir'])->format('d-m-Y') }}</td>
                                        <td>{{ $riwayatkerjas['periode'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Tidak ada data karier</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Jadwal Kenaikan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Data Kenaikan Gaji & Pangkat</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kenaikan Gaji Berkala Terakhir</th>
                                    <th>Kenaikan Pangkat Terakhir</th>
                                    <th>Lama Penundaan</th>
                                    <th>Jadwal Kenaikan Gaji Berikutnya</th>
                                    <th>Jadwal Kenaikan Pangkat Berikutnya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($tglberkala)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($tglpangkat)->format('d-m-Y') }}</td>
                                    <td>{{ $tunda }} Bulan</td>
                                    <td>{{ \Carbon\Carbon::parse($jdber)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($jdpang)->format('d-m-Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Data Penghasilan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Rincian Penghasilan</div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr><th width="60%">Gaji Pokok</th><td width="5%">:</td><td class="text-right">@currency($gapokpeg)</td></tr>
                                    <tr><th>Tunjangan Istri / Suami</th><td>:</td><td class="text-right">@currency($tunjanganistri)</td></tr>
                                    <tr><th>Tunjangan Anak</th><td>:</td><td class="text-right">@currency($tunjangananak)</td></tr>
                                    <tr><th>Tunjangan Pangan</th><td>:</td><td class="text-right">@currency($pangan)</td></tr>
                                    <tr><th>Tunjangan Kinerja</th><td>:</td><td class="text-right">@currency($tuncabang)</td></tr>
                                    <tr><th>Tunjangan BPJS Tenaga Kerja</th><td>:</td><td class="text-right">@currency($bpjstk)</td></tr>
                                    <tr><th>Tunjangan BPJS Kesehatan</th><td>:</td><td class="text-right">@currency($bpjsks)</td></tr>
                                    <tr><th>Tunjangan Program Pensiun</th><td>:</td><td class="text-right">@currency($tunpen)</td></tr>
                                    <tr><th>Tunjangan PPH Pasal 21</th><td>:</td><td class="text-right">@currency($pph)</td></tr>
                                    <tr><th>Tunjangan Jabatan</th><td>:</td><td class="text-right">@currency($tunjab)</td></tr>
                                    <tr><th class="border-bottom pb-2">Tunjangan Fungsional</th><td class="border-bottom pb-2">:</td><td class="border-bottom pb-2 text-right">@currency($fungsi)</td></tr>
                                    <tr class="bg-light">
                                        <th class="pt-2 font-weight-bold">Total Penghasilan</th>
                                        <td class="pt-2 font-weight-bold">:</td>
                                        <td class="pt-2 text-right font-weight-bold text-success">@currency($total)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Pelatihan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold">Data Pelatihan & Sertifikasi</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Pelatihan</th>
                                    <th>Penyelenggara Pelatihan</th>
                                    <th>Tahun Pelatihan</th>
                                    <th class="text-center">Sertifikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelatihan as $latih)
                                    <tr>
                                        <td>{{ $latih->name }}</td>
                                        <td>{{ $latih->penyelenggara }}</td>
                                        <td>{{ $latih->thnlatih }}</td>
                                        <td class="text-center">
                                            @if($latih->image)
                                                <button type="button" class="btn btn-sm btn-outline-info preview-cert" 
                                                    data-src="{{ asset('storage/' . $latih->image) }}" 
                                                    data-type="{{ pathinfo(storage_path('app/public/' . basename($latih->image)), PATHINFO_EXTENSION) }}" 
                                                    title="Preview Sertifikat">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Tidak ada data pelatihan</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    @include('partials.cert_preview')
@endpush