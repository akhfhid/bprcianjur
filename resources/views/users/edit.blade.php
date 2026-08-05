@extends('layouts.global')
@section('title') Edit User @endsection
@section('content')

<style>
    /* ===== EDIT USER – MODERN REDESIGN ===== */
    .eu-wrapper {
        max-width: 860px;
        margin: 0 auto;
    }

    /* Page header */
    .eu-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .eu-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(79,70,229,.35);
    }
    .eu-header h4 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: #1e1b4b;
    }
    .eu-header p {
        margin: 2px 0 0;
        font-size: .85rem;
        color: #6b7280;
    }

    /* Card sections */
    .eu-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 16px rgba(0,0,0,.07);
        margin-bottom: 22px;
        overflow: hidden;
    }
    .eu-card-header {
        padding: 16px 24px;
        background: linear-gradient(135deg, #f8f7ff 0%, #ede9fe 100%);
        border-bottom: 1px solid #e0d9fb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .eu-card-header i {
        color: #7c3aed;
        font-size: 15px;
        width: 20px;
        text-align: center;
    }
    .eu-card-header span {
        font-weight: 600;
        font-size: .92rem;
        color: #3730a3;
        letter-spacing: .01em;
    }
    .eu-card-body {
        padding: 22px 24px;
    }

    /* Field rows */
    .eu-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }
    .eu-row.full { grid-template-columns: 1fr; }
    .eu-row.three { grid-template-columns: 1fr 1fr 1fr; }

    .eu-field { display: flex; flex-direction: column; }
    .eu-label {
        font-size: .78rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 6px;
    }
    .eu-label .req { color: #ef4444; margin-left: 2px; }

    .eu-input, .eu-select, .eu-textarea {
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
    .eu-input:focus, .eu-select:focus, .eu-textarea:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124,58,237,.12);
        background: #fff;
    }
    .eu-input:disabled, .eu-select:disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .eu-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237c3aed' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    .eu-textarea { resize: vertical; min-height: 90px; }

    /* Radio group */
    .eu-radio-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .eu-radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        font-size: .88rem;
        font-weight: 500;
        color: #374151;
        transition: all .18s;
        background: #fafafa;
        user-select: none;
    }
    .eu-radio-label:hover { border-color: #7c3aed; background: #f5f3ff; }
    .eu-radio-label input[type="radio"] { display: none; }
    .eu-radio-label.active-option {
        border-color: #7c3aed;
        background: #f5f3ff;
        color: #6d28d9;
    }
    .eu-radio-label .eu-dot {
        width: 16px; height: 16px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        transition: all .18s;
        flex-shrink: 0;
    }
    .eu-radio-label.active-option .eu-dot {
        border-color: #7c3aed;
        background: #7c3aed;
        box-shadow: inset 0 0 0 3px #fff;
    }

    /* Avatar section */
    .eu-avatar-wrap {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .eu-avatar-preview {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ede9fe;
        box-shadow: 0 2px 10px rgba(0,0,0,.1);
        flex-shrink: 0;
    }
    .eu-avatar-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.6rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 3px solid #ede9fe;
    }
    .eu-avatar-upload {
        flex: 1;
    }
    .eu-file-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: #f5f3ff;
        border: 1.5px dashed #a78bfa;
        border-radius: 10px;
        color: #7c3aed;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .18s;
        width: 100%;
        justify-content: center;
    }
    .eu-file-btn:hover { background: #ede9fe; border-color: #7c3aed; }
    .eu-file-btn input[type="file"] { display: none; }

    /* Cabang badge info */
    .eu-cabang-info {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        border: 1.5px solid #6ee7b7;
        color: #065f46;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .eu-cabang-none {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fef3c7;
        border: 1.5px solid #fcd34d;
        color: #92400e;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    /* Pegawai info pill */
    .eu-pegawai-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        color: #1e40af;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: .85rem;
        font-weight: 600;
        margin-bottom: 14px;
    }
    .eu-pegawai-none {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff7ed;
        border: 1.5px solid #fed7aa;
        color: #9a3412;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: .85rem;
        font-weight: 600;
        margin-bottom: 14px;
    }

    /* Submit area */
    .eu-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding: 20px 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    .eu-btn-cancel {
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
    .eu-btn-cancel:hover { border-color: #9ca3af; color: #374151; text-decoration: none; }
    .eu-btn-save {
        padding: 11px 28px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all .18s;
        box-shadow: 0 4px 14px rgba(79,70,229,.3);
    }
    .eu-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(79,70,229,.4); }
    .eu-btn-save:active { transform: translateY(0); }

    /* Alert */
    .eu-alert-success {
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

    /* Helper text */
    .eu-hint { font-size: .76rem; color: #9ca3af; margin-top: 4px; }

    @media (max-width: 600px) {
        .eu-row, .eu-row.three { grid-template-columns: 1fr; }
        .eu-avatar-wrap { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="eu-wrapper">

    {{-- Page Header --}}
    <div class="eu-header">
        <div class="eu-header-icon"><i class="fas fa-user-edit"></i></div>
        <div>
            <h4>Edit User</h4>
            <p>Perbarui informasi akun dan sinkronisasi data cabang pegawai</p>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('status'))
        <div class="eu-alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="eu-alert-success" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b;">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form enctype="multipart/form-data"
          action="{{ route('users.update_profile', [$user->id]) }}"
          method="POST">
        @csrf

        {{-- ── Informasi Akun ── --}}
        <div class="eu-card">
            <div class="eu-card-header">
                <i class="fas fa-id-card"></i>
                <span>Informasi Akun</span>
            </div>
            <div class="eu-card-body">

                {{-- Avatar --}}
                <div class="eu-row full" style="margin-bottom:20px;">
                    <div class="eu-field">
                        <div class="eu-label">Foto Profil</div>
                        <div class="eu-avatar-wrap">
                            @if($user->avatar)
                                <img src="{{ asset('storage/'.$user->avatar) }}"
                                     alt="{{ $user->username }}"
                                     class="eu-avatar-preview"
                                     id="avatarPreview">
                            @else
                                <div class="eu-avatar-placeholder" id="avatarPlaceholder">
                                    {{ strtoupper(substr($user->username, 0, 2)) }}
                                </div>
                                <img src="" alt="" class="eu-avatar-preview" id="avatarPreview"
                                     style="display:none;">
                            @endif
                            <div class="eu-avatar-upload">
                                <label class="eu-file-btn" for="avatarInput">
                                    <i class="fas fa-camera"></i>
                                    Pilih Foto Baru
                                    <input type="file" name="avatar" id="avatarInput" accept="image/*">
                                </label>
                                <div class="eu-hint">Format: JPG, PNG, GIF. Kosongkan jika tidak ingin mengubah foto.</div>
                                <div class="eu-hint" id="fileNameDisplay" style="margin-top:6px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="eu-row">
                    <div class="eu-field">
                        <label class="eu-label" for="eu_name">Nama Lengkap</label>
                        <input class="eu-input" type="text" id="eu_name" name="name"
                               value="{{ $user->name }}" placeholder="Nama lengkap">
                    </div>
                    <div class="eu-field">
                        <label class="eu-label" for="eu_username">Username</label>
                        <input class="eu-input" type="text" id="eu_username" name="username"
                               value="{{ $user->username }}" disabled>
                        <div class="eu-hint">Username tidak dapat diubah.</div>
                    </div>
                </div>

                <div class="eu-row">
                    <div class="eu-field">
                        <label class="eu-label" for="eu_email">Email</label>
                        <input class="eu-input" type="email" id="eu_email"
                               value="{{ $user->email }}" disabled>
                        <div class="eu-hint">Email hanya bisa diubah dari panel lain.</div>
                    </div>
                    <div class="eu-field">
                        <label class="eu-label" for="eu_phone">Nomor Handphone</label>
                        <input class="eu-input" type="text" id="eu_phone" name="phone"
                               value="{{ $user->phone }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <div class="eu-row full">
                    <div class="eu-field">
                        <label class="eu-label" for="eu_address">Alamat</label>
                        <textarea class="eu-textarea" id="eu_address" name="address"
                                  placeholder="Alamat lengkap">{{ $user->address }}</textarea>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Pengaturan Akses ── --}}
        <div class="eu-card">
            <div class="eu-card-header">
                <i class="fas fa-shield-alt"></i>
                <span>Pengaturan Akses</span>
            </div>
            <div class="eu-card-body">

                <div class="eu-row">
                    <div class="eu-field">
                        <label class="eu-label">Roles / Hak Akses <span class="req">*</span></label>
                        <select class="eu-select" name="roles" id="eu_roles">
                            @foreach ($roles as $role => $name)
                                <option value="{{ $name }}"
                                    {{ $user->roles == $name ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="eu-field">
                        <label class="eu-label">Log Akses</label>
                        <select class="eu-select" name="log" id="eu_log">
                            <option value="TIDAK" {{ $user->loguser == 'TIDAK' ? 'selected' : '' }}>Tidak</option>
                            <option value="YA" {{ $user->loguser == 'YA' ? 'selected' : '' }}>Ya</option>
                        </select>
                        <div class="eu-hint">Rekam aktivitas login pengguna.</div>
                    </div>
                </div>

                <div class="eu-row full">
                    <div class="eu-field">
                        <label class="eu-label">Status Akun <span class="req">*</span></label>
                        <div class="eu-radio-group" id="statusGroup">
                            <label class="eu-radio-label {{ $user->status == 'ACTIVE' ? 'active-option' : '' }}"
                                   id="lbl_active">
                                <input type="radio" name="status" value="ACTIVE"
                                       {{ $user->status == 'ACTIVE' ? 'checked' : '' }}>
                                <span class="eu-dot"></span>
                                <i class="fas fa-check-circle" style="color:#10b981;"></i>
                                Aktif
                            </label>
                            <label class="eu-radio-label {{ $user->status == 'INACTIVE' ? 'active-option' : '' }}"
                                   id="lbl_inactive">
                                <input type="radio" name="status" value="INACTIVE"
                                       {{ $user->status == 'INACTIVE' ? 'checked' : '' }}>
                                <span class="eu-dot"></span>
                                <i class="fas fa-times-circle" style="color:#ef4444;"></i>
                                Tidak Aktif
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Data Pegawai & Cabang ── --}}
        <div class="eu-card">
            <div class="eu-card-header">
                <i class="fas fa-building"></i>
                <span>Data Pegawai & Cabang</span>
            </div>
            <div class="eu-card-body">

                {{-- Pegawai linked info --}}
                @if($pegawai)
                    <div class="eu-pegawai-pill">
                        <i class="fas fa-user-tie"></i>
                        Terhubung ke: <strong>{{ $pegawai->name }}</strong>
                        &nbsp;·&nbsp; ID: {{ $pegawai->id }}
                    </div>
                @else
                    <div class="eu-pegawai-none">
                        <i class="fas fa-exclamation-triangle"></i>
                        Akun ini belum terhubung ke data pegawai.
                    </div>
                @endif

                <input type="hidden" name="pegawai_id" value="{{ $pegawai ? $pegawai->id : '' }}">

                <div class="eu-row">
                    <div class="eu-field">
                        <label class="eu-label">Cabang / Kantor <span class="req">*</span></label>

                        {{-- Info cabang saat ini --}}
                        @if($currentCabang)
                            <div class="eu-cabang-info" style="margin-bottom:8px;">
                                <i class="fas fa-map-marker-alt"></i>
                                Saat ini: {{ $currentCabang->name }}
                            </div>
                        @else
                            <div class="eu-cabang-none" style="margin-bottom:8px;">
                                <i class="fas fa-question-circle"></i>
                                Cabang belum diatur
                            </div>
                        @endif

                        <select class="eu-select" name="cabang_id" id="eu_cabang">
                            <option value="">— Pilih Cabang —</option>
                            @foreach($cabangs as $cabId => $cabName)
                                <option value="{{ $cabId }}"
                                    {{ $activeCabangId == $cabId ? 'selected' : '' }}>
                                    {{ $cabName }}
                                </option>
                            @endforeach
                        </select>
                        <div class="eu-hint">Perubahan cabang akan otomatis tersinkron ke data pegawai.</div>
                    </div>

                    @if($pegawai)
                    <div class="eu-field">
                        <label class="eu-label">Info Jabatan</label>
                        <input class="eu-input" type="text"
                               value="{{ $pegawai->relJabatan ? $pegawai->relJabatan->name : '—' }}"
                               disabled>
                        <div class="eu-hint">Jabatan dikelola di menu Data Pegawai.</div>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="eu-card" style="overflow:visible;">
            <div class="eu-actions">
                <a href="{{ route('users.index') }}" class="eu-btn-cancel">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="eu-btn-save">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Radio status styling ── */
    const radios = document.querySelectorAll('#statusGroup input[type="radio"]');
    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('#statusGroup .eu-radio-label').forEach(function (lbl) {
                lbl.classList.remove('active-option');
            });
            if (this.checked) {
                this.closest('.eu-radio-label').classList.add('active-option');
            }
        });
    });

    /* ── Avatar preview ── */
    var avatarInput = document.getElementById('avatarInput');
    var avatarPreview = document.getElementById('avatarPreview');
    var avatarPlaceholder = document.getElementById('avatarPlaceholder');
    var fileNameDisplay = document.getElementById('fileNameDisplay');

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            var file = this.files[0];
            if (file) {
                fileNameDisplay.textContent = '📎 ' + file.name;
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (avatarPreview) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = '';
                    }
                    if (avatarPlaceholder) {
                        avatarPlaceholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

@endsection
