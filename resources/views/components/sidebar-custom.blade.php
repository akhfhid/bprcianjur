<style>
    .sidebar-custom-wrapper {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        padding: 4px 10px 24px 10px;
    }

    .sidebar-section {
        font-size: 10.5px;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin: 16px 0 6px 10px;
        padding-bottom: 2px;
    }

    .sidebar-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu-list li {
        margin-bottom: 3px;
    }

    .sidebar-menu-list li a {
        display: flex;
        align-items: center;
        padding: 9px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        border-radius: 8px;
        text-decoration: none !important;
        transition: all 0.2s ease-in-out;
        position: relative;
    }

    .sidebar-menu-list li a:hover {
        background-color: #f1f5f9;
        color: #0284c7;
        transform: translateX(3px);
    }

    .sidebar-menu-list li.active a,
    .sidebar-menu-list li a.active {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }

    .sidebar-menu-list li.active a .oi,
    .sidebar-menu-list li a.active .oi {
        color: #ffffff !important;
    }

    .sidebar-menu-list .oi {
        font-size: 14px;
        margin-right: 11px;
        color: #64748b;
        width: 18px;
        text-align: center;
        transition: color 0.2s ease;
    }

    .sidebar-menu-list li a:hover .oi {
        color: #0284c7;
    }

    .sidebar-custom-badge {
        font-size: 9px;
        background: linear-gradient(135deg, #fef08a 0%, #fde047 100%);
        color: #854d0e;
        border-radius: 4px;
        padding: 2px 6px;
        margin-left: auto;
        font-weight: 700;
        letter-spacing: 0.3px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
</style>

@php
    $authUser     = auth()->user();
    $role         = $authUser->roles;
    $customPerms  = $authUser->menu_permissions ?? [];

    $roleDefaultMenus = [
        'ADMIN' => [
            'users', 'loguser', 'setuser',
            'pegawai', 'jabatan', 'pangkat', 'cabang',
            'riwayat', 'keluarga', 'pelatihan', 'berkala',
            'penghasilan', 'gaji',
            'cuti', 'ordercuti',
            'mutasi', 'mutasipangkat',
            'peraturan', 'categories',
            'asisten_sikap', 'wa_setting', 'resetpassword',
        ],
        'ADMIN_SDM' => [
            'pegawai', 'cabang',
            'riwayat', 'keluarga', 'pelatihan', 'berkala',
            'penghasilan', 'gaji',
            'cuti', 'ordercuti',
            'setuser',
        ],
        'STAFF_SDM' => [
            'pegawai',
            'riwayat', 'keluarga', 'pelatihan', 'berkala',
            'penghasilan', 'gaji',
            'cuti',
        ],
        'SUPERVISOR' => ['cuti', 'pegawai', 'peraturan'],
        'PINCAB'     => ['cuti', 'pegawai', 'peraturan'],
        'KADIV'      => ['cuti', 'pegawai', 'peraturan', 'mutasipangkat'],
        'DIRUT'      => ['cuti', 'pegawai', 'peraturan', 'mutasipangkat'],
        'DIRBIS'     => ['cuti', 'pegawai', 'peraturan', 'mutasipangkat'],
        'PATUH'      => ['peraturan', 'kepatuhan', 'pegawai'],
        'USER'       => ['cuti', 'peraturan'],
    ];

    $roleMenus = $roleDefaultMenus[$role] ?? [];
    // Jika user memiliki custom menu_permissions (array), gunakan persis array custom tsb.
    // Jika null, gunakan menu default dari role user.
    $perms = is_array($authUser->menu_permissions) ? $authUser->menu_permissions : $roleMenus;

    $getRoute = function($menuKey) use ($role) {
        switch ($menuKey) {
            case 'pegawai':
                if ($role == 'PINCAB') return route('pincab.indexpegawai');
                if ($role == 'KADIV') return route('kadiv.indexpegawai');
                if ($role == 'SUPERVISOR') return route('supervisor.indexpegawai');
                if ($role == 'DIRUT') return route('direksi.indexpegawai');
                if ($role == 'DIRBIS') return route('dirbis.indexpegawai');
                if ($role == 'PATUH') return route('kepatuhan.indexpegawai');
                return route('pegawai.index');

            case 'peraturan':
                if ($role == 'USER') return route('staff.peraturan');
                if ($role == 'SUPERVISOR') return route('supervisor.peraturan');
                if ($role == 'PINCAB') return route('pincab.peraturan');
                if ($role == 'KADIV') return route('kadiv.peraturan');
                if ($role == 'DIRUT') return route('direksi.peraturan');
                if ($role == 'DIRBIS') return route('dirbis.peraturan');
                return route('peraturan.index');

            case 'cuti':
                if ($role == 'USER') return route('staff.cuti');
                if ($role == 'SUPERVISOR') return route('supervisor.cutiindex');
                if ($role == 'PINCAB') return route('pincab.cutiindex');
                if ($role == 'KADIV') return route('kadiv.cutiindex');
                if ($role == 'DIRUT') return route('direksi.cutiindex');
                if ($role == 'DIRBIS') return route('dirbis.cutiindex');
                if ($role == 'PATUH') return route('kepatuhan.cutiindex');
                return route('cuti.index');

            case 'ordercuti':
                if ($role == 'USER') return route('staff.permohonancuti');
                if ($role == 'SUPERVISOR') return route('supervisor.cutisupervisor');
                if ($role == 'PINCAB') return route('pincab.cutipincab');
                if ($role == 'KADIV') return route('kadiv.permohonancuti');
                if ($role == 'DIRUT') return route('direksi.permohonancuti');
                if ($role == 'DIRBIS') return route('dirbis.permohonancuti');
                if ($role == 'PATUH') return route('kepatuhan.permohonancuti');
                return route('ordercuti.indexcuti');

            case 'loguser':
                if ($role == 'PATUH') return route('kepatuhan.loguser');
                return route('Loguser.index');

            case 'mutasipangkat':
                if ($role == 'DIRUT') return route('direksi.mutasipangkat');
                if ($role == 'DIRBIS') return route('dirbis.mutasipangkat');
                return route('mutasipangkat.index');

            default:
                $routesMap = [
                    'users'         => 'users.index',
                    'setuser'       => 'setuser.index',
                    'cabang'        => 'cabang.index',
                    'jabatan'       => 'jabatan.index',
                    'pangkat'       => 'pangkat.index',
                    'riwayat'       => 'riwayatkerja.index',
                    'keluarga'      => 'keluarga.index',
                    'pelatihan'     => 'pelatihan.index',
                    'berkala'       => 'pegawai.listberkala',
                    'penghasilan'   => 'penghasilan.index',
                    'gaji'          => 'gaji.index',
                    'mutasi'        => 'mutasi.index',
                    'categories'    => 'categories.index',
                    'kepatuhan'     => 'kepatuhan.statusatur',
                    'asisten_sikap' => 'admin.asisten-sikap.index',
                    'wa_setting'    => 'admin.wa-setting.index',
                    'resetpassword' => 'resetpassword.index',
                ];
                if (isset($routesMap[$menuKey]) && \Route::has($routesMap[$menuKey])) {
                    return route($routesMap[$menuKey]);
                }
                return '#';
        }
    };
@endphp

<div class="sidebar-custom-wrapper">
    <ul class="sidebar-menu-list">
        {{-- Dashboard --}}
        <div class="sidebar-section">MAIN</div>
        <li class="{{ request()->is('home*') ? 'active' : '' }}">
            <a href="/home">
                <span class="oi oi-dashboard"></span>
                Dashboard
            </a>
        </li>

        {{-- MANAJEMEN USER --}}
        @if (in_array('users', $perms) || in_array('loguser', $perms) || in_array('setuser', $perms))
            <div class="sidebar-section">USER & AKSES</div>

            @if (in_array('users', $perms))
                <li class="{{ request()->is('users*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('users') }}">
                        <span class="oi oi-person"></span>
                        Users
                    </a>
                </li>
            @endif

            @if (in_array('setuser', $perms))
                <li class="{{ request()->is('setuser*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('setuser') }}">
                        <span class="oi oi-lock-locked"></span>
                        Setup Otorisasi Cuti
                    </a>
                </li>
            @endif

            @if (in_array('loguser', $perms))
                <li class="{{ request()->is('*loguser*') || request()->is('*Loguser*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('loguser') }}">
                        <span class="oi oi-list"></span>
                        Log Akses User
                    </a>
                </li>
            @endif
        @endif

        {{-- MASTER DATA --}}
        @if (in_array('pegawai', $perms) || in_array('jabatan', $perms) || in_array('pangkat', $perms) || in_array('cabang', $perms))
            <div class="sidebar-section">MASTER DATA</div>

            @if (in_array('pegawai', $perms))
                <li class="{{ request()->is('*pegawai*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('pegawai') }}">
                        <span class="oi oi-people"></span>
                        Pegawai
                    </a>
                </li>
            @endif

            @if (in_array('cabang', $perms))
                <li class="{{ request()->is('cabang*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('cabang') }}">
                        <span class="oi oi-map-marker"></span>
                        Kantor
                    </a>
                </li>
            @endif

            @if (in_array('jabatan', $perms))
                <li class="{{ request()->is('jabatan*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('jabatan') }}">
                        <span class="oi oi-briefcase"></span>
                        Jabatan
                    </a>
                </li>
            @endif

            @if (in_array('pangkat', $perms))
                <li class="{{ request()->is('pangkat*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('pangkat') }}">
                        <span class="oi oi-chevron-top"></span>
                        Pangkat
                    </a>
                </li>
            @endif
        @endif

        {{-- RIWAYAT PEGAWAI --}}
        @if (in_array('riwayat', $perms) || in_array('keluarga', $perms) || in_array('pelatihan', $perms) || in_array('berkala', $perms))
            <div class="sidebar-section">KEPEGAWAIAN</div>

            @if (in_array('riwayat', $perms))
                <li class="{{ request()->is('riwayatkerja*') ? 'active' : '' }}">
                    <a href="{{ route('riwayatkerja.index') }}">
                        <span class="oi oi-clipboard"></span>
                        Riwayat Kerja
                    </a>
                </li>
                <li class="{{ request()->is('riwayatpendi*') ? 'active' : '' }}">
                    <a href="{{ route('riwayatpendi.index') }}">
                        <span class="oi oi-clipboard"></span>
                        Riwayat Pendidikan
                    </a>
                </li>
                <li class="{{ request()->is('riwayatangkat*') ? 'active' : '' }}">
                    <a href="{{ route('riwayatangkat.index') }}">
                        <span class="oi oi-clipboard"></span>
                        Riwayat Pengangkatan
                    </a>
                </li>
            @endif

            @if (in_array('keluarga', $perms))
                <li class="{{ request()->is('keluarga*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('keluarga') }}">
                        <span class="oi oi-heart"></span>
                        Data Keluarga
                    </a>
                </li>
            @endif

            @if (in_array('pelatihan', $perms))
                <li class="{{ request()->is('pelatihan*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('pelatihan') }}">
                        <span class="oi oi-star"></span>
                        Pelatihan
                    </a>
                </li>
            @endif

            @if (in_array('berkala', $perms))
                <li class="{{ request()->is('*berkala*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('berkala') }}">
                        <span class="oi oi-timer"></span>
                        Jadwal Kepangkatan
                    </a>
                </li>
            @endif
        @endif

        {{-- KEUANGAN --}}
        @if (in_array('penghasilan', $perms) || in_array('gaji', $perms))
            <div class="sidebar-section">KEUANGAN</div>

            @if (in_array('penghasilan', $perms))
                <li class="{{ request()->is('penghasilan*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('penghasilan') }}">
                        <span class="oi oi-dollar"></span>
                        Penghasilan
                    </a>
                </li>
            @endif

            @if (in_array('gaji', $perms))
                <li class="{{ request()->is('gaji*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('gaji') }}">
                        <span class="oi oi-dollar"></span>
                        Gaji
                    </a>
                </li>
            @endif
        @endif

        {{-- CUTI --}}
        @if (in_array('cuti', $perms) || in_array('ordercuti', $perms))
            <div class="sidebar-section">CUTI</div>

            @if (in_array('cuti', $perms))
                <li class="{{ request()->is('*cuti*') && !request()->is('*permohonan*') && !request()->is('*mintacuti*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('cuti') }}">
                        <span class="oi oi-inbox"></span>
                        Otorisasi / Daftar Cuti
                    </a>
                </li>
            @endif

            @if (in_array('ordercuti', $perms))
                <li class="{{ request()->is('*permohonan*') || request()->is('*cutipincab*') || request()->is('*cutisupervisor*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('ordercuti') }}">
                        <span class="oi oi-envelope-open"></span>
                        Permohonan Cuti
                    </a>
                </li>
            @endif
        @endif

        {{-- MUTASI --}}
        @if (in_array('mutasi', $perms) || in_array('mutasipangkat', $perms))
            <div class="sidebar-section">MUTASI</div>

            @if (in_array('mutasi', $perms))
                <li class="{{ request()->is('mutasi*') && !request()->is('mutasipangkat*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('mutasi') }}">
                        <span class="oi oi-transfer"></span>
                        Mutasi Jabatan
                    </a>
                </li>
            @endif

            @if (in_array('mutasipangkat', $perms))
                <li class="{{ request()->is('*mutasipangkat*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('mutasipangkat') }}">
                        <span class="oi oi-transfer"></span>
                        Mutasi Pangkat
                    </a>
                </li>
            @endif
        @endif

        {{-- PERATURAN & KEPATUHAN --}}
        @if (in_array('peraturan', $perms) || in_array('kepatuhan', $perms) || in_array('categories', $perms))
            <div class="sidebar-section">PERATURAN</div>

            @if (in_array('peraturan', $perms))
                <li class="{{ request()->is('*peraturan*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('peraturan') }}">
                        <span class="oi oi-book"></span>
                        Peraturan
                    </a>
                </li>
            @endif

            @if (in_array('categories', $perms))
                <li class="{{ request()->is('categories*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('categories') }}">
                        <span class="oi oi-tags"></span>
                        Kategori Peraturan
                    </a>
                </li>
            @endif

            @if (in_array('kepatuhan', $perms))
                <li class="{{ request()->is('kepatuhan*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('kepatuhan') }}">
                        <span class="oi oi-shield"></span>
                        Kepatuhan
                    </a>
                </li>
            @endif
        @endif

        {{-- ADMINISTRASI --}}
        @if (in_array('asisten_sikap', $perms) || in_array('wa_setting', $perms) || in_array('resetpassword', $perms))
            <div class="sidebar-section">ADMINISTRASI</div>

            @if (in_array('asisten_sikap', $perms))
                <li class="{{ request()->is('*asisten-sikap*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('asisten_sikap') }}">
                        <span class="oi oi-chat"></span>
                        Asisten Sikap (AI)
                    </a>
                </li>
            @endif

            @if (in_array('wa_setting', $perms))
                <li class="{{ request()->is('*wa-setting*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('wa_setting') }}">
                        <span class="oi oi-cog"></span>
                        Pengaturan WA
                    </a>
                </li>
            @endif

            @if (in_array('resetpassword', $perms))
                <li class="{{ request()->is('resetpassword*') ? 'active' : '' }}">
                    <a href="{{ $getRoute('resetpassword') }}">
                        <span class="oi oi-key"></span>
                        Reset Password
                    </a>
                </li>
            @endif
        @endif
    </ul>
</div>
