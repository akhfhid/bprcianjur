@extends('layouts.global')
@section('title') Edit Data Pegawai @endsection
@section('content')

<style>
    /* ===== EDIT PEGAWAI – MODERN REDESIGN ===== */
    .ep-wrapper {
        max-width: 920px;
        margin: 0 auto;
    }

    /* Page header */
    .ep-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .ep-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(16,185,129,.35);
    }
    .ep-header h4 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: #064e3b;
    }
    .ep-header p {
        margin: 2px 0 0;
        font-size: .85rem;
        color: #6b7280;
    }

    /* Card sections */
    .ep-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 16px rgba(0,0,0,.07);
        margin-bottom: 22px;
        overflow: hidden;
    }
    .ep-card-header {
        padding: 16px 24px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-bottom: 1px solid #a7f3d0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ep-card-header i {
        color: #059669;
        font-size: 15px;
        width: 20px;
        text-align: center;
    }
    .ep-card-header span {
        font-weight: 600;
        font-size: .92rem;
        color: #065f46;
        letter-spacing: .01em;
    }
    .ep-card-body {
        padding: 22px 24px;
    }

    /* Field rows */
    .ep-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }
    .ep-row.full { grid-template-columns: 1fr; }
    .ep-row.three { grid-template-columns: 1fr 1fr 1fr; }

    .ep-field { display: flex; flex-direction: column; }
    .ep-label {
        font-size: .78rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 6px;
    }
    .ep-label .req { color: #ef4444; margin-left: 2px; }

    .ep-input, .ep-select, .ep-textarea {
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: .9rem;
        color: #111827;
        background: #fafafa;
        transition: border-color .18s, box-shadow .18s, background .18s;
        width: 100%;
        appearance: none;
        -webkit-appearance: none;
    }
    .ep-input:focus, .ep-select:focus, .ep-textarea:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16,185,129,.12);
        background: #fff;
    }
    .ep-input:disabled, .ep-select:disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .ep-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    .ep-textarea { resize: vertical; min-height: 90px; }

    /* Photo section */
    .ep-avatar-wrap {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .ep-avatar-preview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #a7f3d0;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
        flex-shrink: 0;
    }
    .ep-avatar-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #059669, #10b981);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.6rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 3px solid #a7f3d0;
    }
    .ep-avatar-upload {
        flex: 1;
    }
    .ep-file-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: #ecfdf5;
        border: 1.5px dashed #34d399;
        border-radius: 10px;
        color: #059669;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .18s;
        width: 100%;
        justify-content: center;
    }
    .ep-file-btn:hover { background: #d1fae5; border-color: #059669; }
    .ep-file-btn input[type="file"] { display: none; }

    /* Submit area */
    .ep-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding: 20px 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    .ep-btn-cancel {
        padding: 11px 24px;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        background: #fff;
        color: #6b7280;
        font-weight: 600;
        font-size: .9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all .18s;
    }
    .ep-btn-cancel:hover { border-color: #9ca3af; color: #374151; text-decoration: none; }
    .ep-btn-save {
        padding: 11px 28px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all .18s;
        box-shadow: 0 4px 14px rgba(16,185,129,.3);
    }
    .ep-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,.4); }
    .ep-btn-save:active { transform: translateY(0); }

    /* Alert */
    .ep-alert-success {
        padding: 12px 18px;
        background: #ecfdf5;
        border: 1.5px solid #6ee7b7;
        border-radius: 12px;
        color: #065f46;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        font-size: .9rem;
        font-weight: 500;
    }

    .ep-hint { font-size: .76rem; color: #9ca3af; margin-top: 4px; }

    @media (max-width: 600px) {
        .ep-row, .ep-row.three { grid-template-columns: 1fr; }
        .ep-avatar-wrap { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="ep-wrapper">

    {{-- Page Header --}}
    <div class="ep-header">
        <div class="ep-header-icon"><i class="fas fa-user-edit"></i></div>
        <div>
            <h4>Edit Data Pegawai</h4>
            <p>Perbarui rincian identitas, kepegawaian, dan tunjangan kinerja pegawai</p>
        </div>
    </div>

    {{-- Alert Status --}}
    @if(session('status'))
        <div class="ep-alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <form enctype="multipart/form-data" method="POST" action="{{ route('pegawai.update', [$pegawai->id]) }}">
        @csrf
        <input type="hidden" name="_method" value="PUT">

        {{-- ── Informasi Personal & Identitas ── --}}
        <div class="ep-card">
            <div class="ep-card-header">
                <i class="fas fa-id-badge"></i>
                <span>Informasi Personal & Identitas</span>
            </div>
            <div class="ep-card-body">

                <div class="ep-row">
                    <div class="ep-field">
                        <label class="ep-label" for="name">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" class="ep-input" id="name" name="name" value="{{ $pegawai->name }}" placeholder="Nama Pegawai">
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="nikpegawai">NIK Kepegawaian</label>
                        <input type="text" class="ep-input" id="nikpegawai" name="nikpegawai" value="{{ $pegawai->nikpegawai }}" placeholder="NIK Kepegawaian">
                    </div>
                </div>

                <div class="ep-row three">
                    <div class="ep-field">
                        <label class="ep-label" for="nikpenduduk">NIK Kependudukan (KTP)</label>
                        <input type="text" class="ep-input" id="nikpenduduk" name="nikpenduduk" value="{{ $pegawai->nikpenduduk }}" placeholder="NIK KTP">
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="templahir">Tempat Lahir</label>
                        <input type="text" class="ep-input" id="templahir" name="templahir" value="{{ $pegawai->templahir }}" placeholder="Tempat Lahir">
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="tgllahir">Tanggal Lahir</label>
                        <input type="date" class="ep-input" id="tgllahir" name="tgllahir" value="{{ $pegawai->tgllahir }}">
                    </div>
                </div>

                <div class="ep-row three">
                    <div class="ep-field">
                        <label class="ep-label" for="jenkel">Jenis Kelamin</label>
                        <select class="ep-select" id="jenkel" name="jenkel">
                            <option value="{{ $pegawai->kelamin }}">{{ $kel }}</option>
                            @foreach($jenkel as $kelamin => $name)
                                <option value="{{ $kelamin }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="agama">Agama</label>
                        <select class="ep-select" id="agama" name="agama">
                            <option value="{{ $pegawai->agama }}">{{ $ag }}</option>
                            @foreach ($agama as $agamas => $name)
                                <option value="{{ $agamas }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="goldar">Golongan Darah</label>
                        <select class="ep-select" id="goldar" name="goldar">
                            <option value="{{ $pegawai->goldar }}">{{ $pegawai->goldar ?: 'Pilih' }}</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>
                </div>

                <div class="ep-row">
                    <div class="ep-field">
                        <label class="ep-label" for="status">Status Perkawinan</label>
                        <select class="ep-select" id="status" name="status">
                            <option value="{{ $pegawai->status }}">{{ $stat }}</option>
                            @foreach ($status as $kawin => $name)
                                <option value="{{ $kawin }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="pendidikan">Pendidikan Terakhir</label>
                        <select class="ep-select" id="pendidikan" name="pendidikan">
                            <option value="{{ $pegawai->pendidikan }}">{{ $pend }}</option>
                            @foreach ($pendidikan as $didik => $name)
                                <option value="{{ $didik }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="ep-row full">
                    <div class="ep-field">
                        <label class="ep-label" for="alamat">Alamat Lengkap</label>
                        <textarea class="ep-textarea" id="alamat" name="alamat" placeholder="Alamat tinggal">{{ $pegawai->alamat }}</textarea>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Kontak & Berkas ── --}}
        <div class="ep-card">
            <div class="ep-card-header">
                <i class="fas fa-address-book"></i>
                <span>Kontak & Foto Profil</span>
            </div>
            <div class="ep-card-body">

                <div class="ep-row three">
                    <div class="ep-field">
                        <label class="ep-label" for="email">Alamat Email</label>
                        <input type="email" class="ep-input" id="email" name="email" value="{{ $pegawai->email }}" placeholder="email@contoh.com">
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="nohp">No. Handphone</label>
                        <input type="text" class="ep-input" id="nohp" name="nohp" value="{{ $pegawai->nohp }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="npwp">NPWP</label>
                        <input type="text" class="ep-input" id="npwp" name="npwp" value="{{ $pegawai->npwp }}" placeholder="Nomor NPWP">
                    </div>
                </div>

                <div class="ep-row full" style="margin-top:10px;">
                    <div class="ep-field">
                        <label class="ep-label">Foto Profil Pegawai</label>
                        <div class="ep-avatar-wrap">
                            @if($pegawai->photo)
                                <img src="{{ asset('storage/'.$pegawai->photo) }}" alt="{{ $pegawai->name }}" class="ep-avatar-preview" id="pegawaiPhotoPreview">
                            @else
                                <div class="ep-avatar-placeholder" id="pegawaiPhotoPlaceholder">
                                    <i class="fas fa-user"></i>
                                </div>
                                <img src="" alt="" class="ep-avatar-preview" id="pegawaiPhotoPreview" style="display:none;">
                            @endif
                            <div class="ep-avatar-upload">
                                <label class="ep-file-btn" for="photoInput">
                                    <i class="fas fa-camera"></i>
                                    Pilih Foto Baru
                                    <input type="file" name="photo" id="photoInput" accept="image/*">
                                </label>
                                <div class="ep-hint">Format: JPG, PNG, GIF. Kosongkan jika tidak ingin mengubah foto.</div>
                                <div class="ep-hint" id="photoFileName" style="margin-top:6px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Status Kepegawaian & Jabatan ── --}}
        <div class="ep-card">
            <div class="ep-card-header">
                <i class="fas fa-briefcase"></i>
                <span>Status Kepegawaian & Jabatan</span>
            </div>
            <div class="ep-card-body">

                <div class="ep-row three">
                    <div class="ep-field">
                        <label class="ep-label" for="tglmasuk">Tanggal Masuk</label>
                        <input type="date" class="ep-input" id="tglmasuk" name="tglmasuk" value="{{ $pegawai->tglmasuk }}">
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="spegawai">Status Pegawai</label>
                        <select class="ep-select" id="spegawai" name="spegawai">
                            <option value="{{ $pegawai->spegawai }}">{{ $spegawai }}</option>
                            @foreach ($tetap as $speg => $name)
                                <option value="{{ $speg }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="kantor">Kantor / Cabang</label>
                        <select class="ep-select" id="kantor" name="kantor">
                            <option value="{{ $pegawai->cabang }}">{{ $kant }}</option>
                            @foreach($kantor as $cabang => $name)
                                <option value="{{ $cabang }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="ep-row three">
                    <div class="ep-field">
                        <label class="ep-label" for="jabatan">Jabatan</label>
                        <select class="ep-select" id="jabatan" name="jabatan">
                            <option value="{{ $pegawai->jabatan }}">{{ $jab }}</option> 
                            @foreach ($jabatan as $jabatans => $name)
                                <option value="{{ $jabatans }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="pangkat">Kepangkatan</label>
                        <select class="ep-select" id="pangkat" name="pangkat">
                            <option value="{{ $pegawai->pangkat }}">{{ $pang }}</option>
                            @foreach ($pangkat as $pangkats => $name)
                                <option value="{{ $pangkats }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ep-field">
                        <label class="ep-label" for="mkpang">Masa Kerja Pangkat</label>
                        <input type="text" class="ep-input" id="mkpang" name="mkpang" value="{{ $pegawai->mkpang }}" placeholder="Tahun/Bulan">
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Pengaturan Tunjangan Kinerja ── --}}
        <div class="ep-card">
            <div class="ep-card-header">
                <i class="fas fa-coins"></i>
                <span>Pengaturan Tunjangan Kinerja</span>
            </div>
            <div class="ep-card-body">

                <div class="ep-row">
                    <div class="ep-field">
                        <label class="ep-label" for="pegawai_tuncab_type">Tipe Tunjangan Kinerja</label>
                        <select class="ep-select" name="tuncab_type" id="pegawai_tuncab_type">
                            <option value="cabang" {{ empty($pegawai->is_custom_tuncab) ? 'selected' : '' }}>
                                Standard (Sesuai Cabang)
                            </option>
                            <option value="custom" {{ !empty($pegawai->is_custom_tuncab) ? 'selected' : '' }}>
                                Custom (Input Persen Manual)
                            </option>
                        </select>
                        <div class="ep-hint">Persen manual tidak berubah saat pindah cabang (*mutasi*).</div>
                    </div>

                    <div class="ep-field" id="pegawai_tuncab_standard_wrap" style="{{ !empty($pegawai->is_custom_tuncab) ? 'display:none;' : '' }}">
                        <label class="ep-label" for="tuncab">Tunjangan Cabang</label>
                        <select class="ep-select" name="tuncab" id="tuncab">
                            <option value="{{ $pegawai->tuncab }}">{{ $kant }}</option>
                            <option value="">— Mengikuti Kantor Cabang —</option>
                            @foreach($kantor as $cabang => $name)
                                <option value="{{ $cabang }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div class="ep-hint">Menggunakan persentase default dari cabang.</div>
                    </div>

                    <div class="ep-field" id="pegawai_tuncab_custom_wrap" style="{{ empty($pegawai->is_custom_tuncab) ? 'display:none;' : '' }}">
                        <label class="ep-label" for="custom_tuncab_val">Persentase Manual (%)</label>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <input class="ep-input" type="number" step="0.01" min="0" max="100" id="custom_tuncab_val" name="custom_tuncab_val"
                                   value="{{ !empty($pegawai->is_custom_tuncab) ? (($pegawai->custom_tuncab_val ?? 0) * 100) : '' }}"
                                   placeholder="Contoh: 10 untuk 10%">
                            <span style="font-weight:700; color:#059669;">%</span>
                        </div>
                        <div class="ep-hint">Persentase khusus yang bersifat tetap.</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="ep-card" style="overflow:visible;">
            <div class="ep-actions">
                <a href="{{ route('pegawai.index') }}" class="ep-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="ep-btn-save">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    /* ── Kantor & Tuncab Sync (Standard Mode) ── */
    var kantorSelect = document.getElementById('kantor');
    var tuncabSelect = document.getElementById('tuncab');
    if (kantorSelect && tuncabSelect) {
        kantorSelect.addEventListener('change', function() {
            var tuncabType = document.getElementById('pegawai_tuncab_type');
            if (tuncabType && tuncabType.value === 'cabang') {
                tuncabSelect.value = this.value;
            }
        });
    }

    /* ── Tunjangan Kinerja Toggle ── */
    var tuncabType = document.getElementById('pegawai_tuncab_type');
    var stdWrap = document.getElementById('pegawai_tuncab_standard_wrap');
    var customWrap = document.getElementById('pegawai_tuncab_custom_wrap');
    if (tuncabType && stdWrap && customWrap) {
        tuncabType.addEventListener('change', function() {
            if (this.value === 'custom') {
                stdWrap.style.display = 'none';
                customWrap.style.display = 'block';
            } else {
                stdWrap.style.display = 'block';
                customWrap.style.display = 'none';
            }
        });
    }

    /* ── Photo Preview ── */
    var photoInput = document.getElementById('photoInput');
    var preview = document.getElementById('pegawaiPhotoPreview');
    var placeholder = document.getElementById('pegawaiPhotoPlaceholder');
    var nameDisplay = document.getElementById('photoFileName');

    if (photoInput) {
        photoInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                if (nameDisplay) nameDisplay.textContent = '📎 ' + file.name;
                var reader = new FileReader();
                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = '';
                    }
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

@endsection
