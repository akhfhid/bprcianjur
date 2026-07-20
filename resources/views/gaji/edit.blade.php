@extends('layouts.global')
@section('title') Edit Data Gaji & Tunjangan @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h3 class="mb-3">Edit Data Gaji & Tunjangan</h3>
            <h5 class="text-muted mb-4">Pegawai: {{ $pegawai->name }} ({{ $pegawai->nikpegawai }})</h5>
            <hr>
        </div>
    </div>

    @if(session('status'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">{{ session('status') }}</div>
        </div>
    </div>
    @endif

    <form enctype="multipart/form-data" action="{{ route('gaji.update', [$gaji->id]) }}" method="POST">
        @csrf
        <input type="hidden" value="PUT" name="_method">
        <input type="hidden" name="idpeg" value="{{ $pegawai->id }}">

        <div class="row">
            <!-- Left Column: Inputs -->
            <div class="col-md-8">
                <!-- Card 1: Pangkat & Gaji Pokok -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-id-card-alt mr-2"></i> Pangkat & Gaji Pokok</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="pangkat"><b>Pangkat/Golongan</b></label>
                                <select name="pangkat" id="pangkat" class="form-control">
                                    @foreach($pangkats as $p)
                                        <option value="{{ $p->id }}" {{ $pegawai->pangkat == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Menentukan dasar lookup Gaji Pokok.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="mkpang"><b>Masa Kerja Pangkat (Tahun)</b></label>
                                <input type="number" name="mkpang" id="mkpang" class="form-control" value="{{ $pegawai->mkpang ?? 0 }}" min="0">
                                <small class="form-text text-muted">Golongan/tahun masa kerja pangkat untuk lookup.</small>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 form-group">
                                <label><b>Gaji Pokok (Terhitung Otomatis)</b></label>
                                <input type="text" id="gapok_display" class="form-control bg-light" readonly style="font-weight: bold; color: #2c3e50;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tunjangan Manual / Mandiri -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-edit mr-2"></i> Tunjangan Manual & Potongan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="jabatan"><b>Tunjangan Jabatan</b></label>
                                <input type="number" name="jabatan" id="jabatan" class="form-control" value="{{ $gaji->jabatan ?? 0 }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="fungsi"><b>Tunjangan Fungsional</b></label>
                                <input type="number" name="fungsi" id="fungsi" class="form-control" value="{{ $gaji->fungsi ?? 0 }}">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4 form-group">
                                <label for="bpjsks"><b>Tunjangan BPJS Kesehatan</b></label>
                                <input type="number" name="bpjsks" id="bpjsks" class="form-control" value="{{ $gaji->bpjsks ?? 0 }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="bpjstk"><b>Tunjangan BPJS Tenaga Kerja</b></label>
                                <input type="number" name="bpjstk" id="bpjstk" class="form-control" value="{{ $gaji->bpjstk ?? 0 }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="pph21"><b>Tunjangan PPH Pasal 21</b></label>
                                <input type="number" name="pph21" id="pph21" class="form-control" value="{{ $gaji->pph ?? 0 }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Computed fields & summary -->
            <div class="col-md-4">
                <!-- Card 3: Tunjangan Otomatis -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator mr-2"></i> Tunjangan Dinamis (Sistem)</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tunjangan Istri / Suami</label>
                            <input type="text" id="tunj_istri_display" class="form-control bg-light" readonly>
                            <small class="form-text text-muted">Berdasarkan {{ $jumlahnikah }} Pasangan (Nikah)</small>
                        </div>
                        <div class="form-group">
                            <label>Tunjangan Anak</label>
                            <input type="text" id="tunj_anak_display" class="form-control bg-light" readonly>
                            <small class="form-text text-muted">Berdasarkan {{ $jumlahanak }} Anak (Maks. 2)</small>
                        </div>
                        <div class="form-group">
                            <label>Tunjangan Pangan</label>
                            <input type="text" id="tunj_pangan_display" class="form-control bg-light" readonly>
                            <small class="form-text text-muted">Berdasarkan jumlah tanggungan keluarga.</small>
                        </div>
                        <div class="form-group">
                            <label>Tunjangan Kinerja</label>
                            <input type="text" id="tunj_kinerja_display" class="form-control bg-light" readonly>
                            <small class="form-text text-muted">Berdasarkan Status ({{ $spegawai->name ?? '-' }}) & Cabang.</small>
                        </div>
                        <div class="form-group">
                            <label>Tunjangan Program Pensiun</label>
                            <input type="text" id="tunj_pensiun_display" class="form-control bg-light" readonly>
                            <small class="form-text text-muted">Berdasarkan Jabatan & Gaji Pokok.</small>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('keluarga.list', $pegawai->id) }}" target="_blank" class="btn btn-outline-info btn-block btn-sm">
                                <i class="fas fa-users mr-1"></i> Kelola Data Keluarga <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total & Submit -->
                <div class="card shadow-sm mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i> Total Penghasilan</h5>
                    </div>
                    <div class="card-body text-center bg-light">
                        <h3 class="text-success my-3" id="total_display">Rp. 0</h3>
                        <p class="text-muted small">Jumlah ini adalah estimasi total kotor penghasilan pegawai per bulan.</p>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('gaji.list', $pegawai->id) }}" class="btn btn-secondary btn-block">Batal</a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-success btn-block">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const berkalas = @json($berkalas);
        const tunis = {{ $jabatan['tunis'] ?? 0 }};
        const tunak = {{ $jabatan['tunak'] ?? 0 }};
        const tunpang = {{ $jabatan['tunpang'] ?? 0 }};
        const pensiun = {{ $jabatan['pensiun'] ?? 0 }};
        const tuncab = {{ ($spegawai && $spegawai->id == 3) ? ($tunkin['tunjangan'] ?? 0) : 0 }};
        const statpegawai = {{ $pegawai['spegawai'] ?? 0 }};
        const jumlahanak = {{ $jumlahanak ?? 0 }};
        const jumlahnikah = {{ $jumlahnikah ?? 0 }};

        const formatCurrency = (val) => {
            return "Rp. " + new Intl.NumberFormat("id-ID").format(Math.round(val));
        };

        const calculateSalary = () => {
            const pangkat = document.getElementById("pangkat").value;
            const mkpang = parseInt(document.getElementById("mkpang").value) || 0;

            // 1. Gaji Pokok Lookup
            const match = berkalas.find(b => b.idpang == pangkat && b.gol == mkpang);
            const gapok = match ? parseFloat(match.gapok) : 0;
            document.getElementById("gapok_display").value = formatCurrency(gapok);

            // 2. Tunjangan Istri/Suami
            let tunj_istri = 0;
            if (statpegawai != 1) {
                tunj_istri = tunis * gapok * jumlahnikah;
            }
            document.getElementById("tunj_istri_display").value = formatCurrency(tunj_istri);

            // 3. Tunjangan Anak
            let tunj_anak = 0;
            if (jumlahanak <= 2) {
                tunj_anak = tunak * gapok * jumlahanak;
            } else if (jumlahanak > 2) {
                tunj_anak = tunak * gapok * 2;
            } else if (statpegawai != 3) {
                tunj_anak = 0;
            }
            document.getElementById("tunj_anak_display").value = formatCurrency(tunj_anak);

            // 4. Tunjangan Pangan
            let jmlkeluarga = jumlahanak > 2 ? (jumlahnikah + 2) : (jumlahnikah + jumlahanak);
            let tunj_pangan = 0;
            if (statpegawai != 1) {
                if (jmlkeluarga > 3) {
                    tunj_pangan = tunpang * 0;
                } else {
                    tunj_pangan = tunpang * (jmlkeluarga + 1);
                }
            }
            document.getElementById("tunj_pangan_display").value = formatCurrency(tunj_pangan);

            // 5. Tunjangan Kinerja
            let tunj_kinerja = tuncab * gapok;
            document.getElementById("tunj_kinerja_display").value = formatCurrency(tunj_kinerja);

            // 6. Tunjangan Program Pensiun
            let tunj_pensiun = 0;
            if (statpegawai == 3) {
                tunj_pensiun = pensiun * gapok;
            }
            document.getElementById("tunj_pensiun_display").value = formatCurrency(tunj_pensiun);

            // 7. Manual values
            const tunj_jabatan = parseFloat(document.getElementById("jabatan").value) || 0;
            const tunj_fungsional = parseFloat(document.getElementById("fungsi").value) || 0;
            const bpjs_kes = parseFloat(document.getElementById("bpjsks").value) || 0;
            const bpjs_tk = parseFloat(document.getElementById("bpjstk").value) || 0;
            const pph_21 = parseFloat(document.getElementById("pph21").value) || 0;

            // 8. Total
            const total = gapok + tunj_istri + tunj_anak + tunj_pangan + tunj_kinerja + tunj_pensiun + tunj_jabatan + tunj_fungsional + bpjs_kes + bpjs_tk + pph_21;
            document.getElementById("total_display").innerText = formatCurrency(total);
        };

        // Add event listeners to trigger recalculation
        document.getElementById("pangkat").addEventListener("change", calculateSalary);
        document.getElementById("mkpang").addEventListener("input", calculateSalary);
        document.getElementById("jabatan").addEventListener("input", calculateSalary);
        document.getElementById("fungsi").addEventListener("input", calculateSalary);
        document.getElementById("bpjsks").addEventListener("input", calculateSalary);
        document.getElementById("bpjstk").addEventListener("input", calculateSalary);
        document.getElementById("pph21").addEventListener("input", calculateSalary);

        // Initial run
        calculateSalary();
    });
</script>
@endsection