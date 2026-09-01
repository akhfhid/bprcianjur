@extends('layouts.global')

@section('title')
    Edit Hak Akses Menu
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0 text-gray-800">
            <i class="fas fa-shield-alt text-warning mr-2"></i>
            Edit Hak Akses Menu
        </h4>
        <small class="text-muted">Atur menu apa saja yang bisa diakses oleh user ini</small>
    </div>
</div>

{{-- Alert status --}}
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Info User --}}
<div class="card shadow mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center">
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle mr-3 shadow-sm"
                    width="55" height="55" style="object-fit: cover;">
            @else
                <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center text-white mr-3 shadow-sm"
                    style="width: 55px; height: 55px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 1.2rem; font-weight: bold;">
                    {{ strtoupper(substr($user->username ?? $user->name, 0, 2)) }}
                </div>
            @endif
            <div>
                <h5 class="mb-0 font-weight-bold">{{ $user->name }}</h5>
                <div class="d-flex align-items-center mt-1">
                    <span class="badge badge-primary mr-2">{{ $user->roles }}</span>
                    @if ($user->menu_permissions !== null)
                        <span class="badge badge-warning text-dark">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Hak Akses Custom ({{ count($user->menu_permissions) }} menu)
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            <i class="fas fa-users mr-1"></i>
                            Menggunakan Default (berdasarkan roles)
                        </span>
                    @endif
                </div>
                <small class="text-muted">{{ $user->email }}</small>
            </div>
        </div>
    </div>
</div>

{{-- Form Hak Akses --}}
<form action="{{ route('users.hak-akses.update', $user->id) }}" method="POST" id="form-hak-akses">
    @csrf

    {{-- Toggle: Gunakan Default vs Custom --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 font-weight-bold text-gray-700">
                <i class="fas fa-cog mr-2"></i> Mode Hak Akses
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="mode_default" name="use_default" value="1"
                            class="custom-control-input" onchange="toggleMenuForm(this)"
                            {{ $user->menu_permissions === null ? 'checked' : '' }}>
                        <label class="custom-control-label" for="mode_default">
                            <strong>Gunakan Default (berdasarkan Roles)</strong>
                            <br>
                            <small class="text-muted">
                                Sidebar dan menu mengikuti hak akses default dari role
                                <span class="badge badge-primary">{{ $user->roles }}</span>
                            </small>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="mode_custom" name="use_default" value="0"
                            class="custom-control-input" onchange="toggleMenuForm(this)"
                            {{ $user->menu_permissions !== null ? 'checked' : '' }}>
                        <label class="custom-control-label" for="mode_custom">
                            <strong>Custom Akses Menu (Override)</strong>
                            <br>
                            <small class="text-muted">
                                Bebas tentukan menu yang aktif untuk user ini.
                                Anda dapat mengaktifkan atau <strong>menonaktifkan</strong> menu default dari role <span class="badge badge-primary">{{ $user->roles }}</span>.
                            </small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel Menu Checkboxes --}}
    <div id="panel-menu-custom" style="{{ $user->menu_permissions === null ? 'display:none;' : '' }}">

        {{-- Toolbar --}}
        <div class="d-flex align-items-center mb-3">
            <button type="button" class="btn btn-sm btn-outline-success mr-2" onclick="checkAll()">
                <i class="fas fa-check-double mr-1"></i> Centang Semua
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger mr-2" onclick="uncheckAll()">
                <i class="fas fa-times mr-1"></i> Hapus Semua
            </button>
            <span class="text-muted small" id="count-label">
                <span id="checked-count">0</span> menu dipilih
            </span>
        </div>

        <div class="row">
    @php
    // Pre-check: menu yang sudah dimiliki oleh roles default user
    $roleDefaultMenus = [
        'ADMIN'      => ['users','loguser','setuser','pegawai','jabatan','pangkat','cabang','riwayat','keluarga','pelatihan','berkala','penghasilan','gaji','cuti','ordercuti','mutasi','mutasipangkat','peraturan','categories','asisten_sikap','wa_setting','resetpassword'],
        'ADMIN_SDM'  => ['pegawai','cabang','riwayat','keluarga','pelatihan','berkala','penghasilan','gaji','cuti','ordercuti','setuser'],
        'STAFF_SDM'  => ['pegawai','riwayat','keluarga','pelatihan','berkala','penghasilan','gaji','cuti'],
        'SUPERVISOR' => ['cuti','pegawai'],
        'PINCAB'     => ['cuti','pegawai','peraturan'],
        'KADIV'      => ['cuti','pegawai','peraturan','mutasipangkat'],
        'DIRUT'      => ['cuti','pegawai','peraturan','mutasipangkat'],
        'DIRBIS'     => ['cuti','pegawai','peraturan','mutasipangkat'],
        'PATUH'      => ['peraturan','kepatuhan','pegawai'],
        'USER'       => [],
    ];
    $roleMenusDefault = $roleDefaultMenus[$user->roles] ?? [];
@endphp
        @foreach ($availableMenus as $group => $menus)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h6 class="mb-0 text-white font-weight-bold">
                            <i class="fas fa-folder-open mr-2"></i>{{ $group }}
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        @foreach ($menus as $key => $label)
                            @php
                                $isRoleDefault  = in_array($key, $roleMenusDefault);
                                if (is_array($user->menu_permissions)) {
                                    // Jika user punya custom permissions, centang persis yang ada di custom array
                                    $isChecked = in_array($key, $user->menu_permissions);
                                } else {
                                    // Jika belum pernah diset custom (null), gunakan default roles
                                    $isChecked = $isRoleDefault;
                                }
                            @endphp
                            <div class="custom-control custom-checkbox mb-2">
                                <input
                                    type="checkbox"
                                    class="custom-control-input menu-checkbox {{ $isRoleDefault ? 'is-role-default' : '' }}"
                                    id="menu_{{ $key }}"
                                    name="menus[]"
                                    value="{{ $key }}"
                                    onchange="updateCount()"
                                    {{ $isChecked ? 'checked' : '' }}>
                                <label class="custom-control-label" for="menu_{{ $key }}">
                                    {{ $label }}
                                    @if ($isRoleDefault)
                                        <span class="badge badge-light border text-muted" style="font-size:9px;" title="Menu default dari role {{ $user->roles }}">default role</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
        </div>

        {{-- Info --}}
        <div class="alert alert-info d-flex align-items-start">
            <i class="fas fa-info-circle mr-2 mt-1"></i>
            <div>
                <strong>Bebas Mengatur Hak Akses Menu:</strong>
                Anda dapat memilih menu apa saja yang aktif untuk user ini.
                Menu default dari role ditandai dengan badge <span class="badge badge-light border text-muted">default role</span>. Anda bisa membiarkan centang untuk mengaktifkan atau menghapus centang untuk <strong>menonaktifkan</strong> menu tersebut.
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="card shadow mb-4">
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div>
                <span class="text-muted small">
                    <i class="fas fa-clock mr-1"></i>
                    Perubahan akan langsung berlaku saat user login berikutnya atau refresh halaman.
                </span>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save mr-1"></i> Simpan Hak Akses
                </button>
            </div>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
    function toggleMenuForm(radio) {
        const panel = document.getElementById('panel-menu-custom');
        if (radio.value === '0') {
            panel.style.display = '';
            updateCount();
        } else {
            panel.style.display = 'none';
        }
    }

    function checkAll() {
        document.querySelectorAll('.menu-checkbox').forEach(cb => cb.checked = true);
        updateCount();
    }

    function uncheckAll() {
        document.querySelectorAll('.menu-checkbox').forEach(cb => cb.checked = false);
        updateCount();
    }

    function updateCount() {
        const count = document.querySelectorAll('.menu-checkbox:checked').length;
        document.getElementById('checked-count').textContent = count;
    }

    // Init count on load
    document.addEventListener('DOMContentLoaded', function () {
        updateCount();
    });
</script>
@endpush
