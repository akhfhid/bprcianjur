<style>
    .sidebar-menu li a {
        font-size: 13px;
        padding: 6px 10px;
    }

    .sidebar-menu .oi {
        font-size: 12px;
        margin-right: 6px;
    }

    .sidebar-section {
        font-size: 10px;
        margin: 10px 0 4px;
        letter-spacing: .5px;
    }

    .sidebar-custom-badge {
        font-size: 9px;
        background: #f6c23e;
        color: #333;
        border-radius: 3px;
        padding: 1px 4px;
        margin-left: 4px;
        font-weight: bold;
        vertical-align: middle;
    }
</style>

@php
    $authUser     = auth()->user();
    $role         = $authUser->roles;
    $customPerms  = $authUser->menu_permissions ?? [];

    /**
     * Menu default yang dimiliki tiap role.
     * Custom permissions bersifat ADDITIVE:
     * user tetap dapat semua menu dari roles-nya,
     * ditambah menu ekstra yang diberikan administrator.
     */
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

    // Gabungkan: role defaults + custom permissions (union, no duplicates)
    $perms = array_unique(array_merge($roleMenus, $customPerms));

    /**
     * Helper penentuan route link per menu berdasarkan role user yang login.
     * Sehingga user tidak terhadang 403 Forbidden karena route disesuaikan Controller role-nya.
     */
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

<div class="sidebar-menu">

    {{-- Dashboard selalu tampil --}}
    <div class="sidebar-section">MAIN</div>
    <li>
        <a href="/home">
            <span class="oi oi-dashboard"></span>
            Dashboard
        </a>
    </li>

    {{-- MANAJEMEN USER --}}
    @if (in_array('users', $perms) || in_array('loguser', $perms) || in_array('setuser', $perms))
        <div class="sidebar-section">USER & AKSES</div>

        @if (in_array('users', $perms))
            <li>
                <a href="{{ $getRoute('users') }}">
                    <span class="oi oi-person"></span>
                    Users
                </a>
            </li>
        @endif

        @if (in_array('setuser', $perms))
            <li>
                <a href="{{ $getRoute('setuser') }}">
                    <span class="oi oi-lock-locked"></span>
                    Setup Otorisasi Cuti
                </a>
            </li>
        @endif

        @if (in_array('loguser', $perms))
            <li>
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
            <li>
                <a href="{{ $getRoute('pegawai') }}">
                    <span class="oi oi-people"></span>
                    Pegawai
                </a>
            </li>
        @endif

        @if (in_array('cabang', $perms))
            <li>
                <a href="{{ $getRoute('cabang') }}">
                    <span class="oi oi-map-marker"></span>
                    Kantor
                </a>
            </li>
        @endif

        @if (in_array('jabatan', $perms))
            <li>
                <a href="{{ $getRoute('jabatan') }}">
                    <span class="oi oi-briefcase"></span>
                    Jabatan
                </a>
            </li>
        @endif

        @if (in_array('pangkat', $perms))
            <li>
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
            <li>
                <a href="{{ route('riwayatkerja.index') }}">
                    <span class="oi oi-clipboard"></span>
                    Riwayat Kerja
                </a>
            </li>
            <li>
                <a href="{{ route('riwayatpendi.index') }}">
                    <span class="oi oi-clipboard"></span>
                    Riwayat Pendidikan
                </a>
            </li>
            <li>
                <a href="{{ route('riwayatangkat.index') }}">
                    <span class="oi oi-clipboard"></span>
                    Riwayat Pengangkatan
                </a>
            </li>
        @endif

        @if (in_array('keluarga', $perms))
            <li>
                <a href="{{ $getRoute('keluarga') }}">
                    <span class="oi oi-heart"></span>
                    Data Keluarga
                </a>
            </li>
        @endif

        @if (in_array('pelatihan', $perms))
            <li>
                <a href="{{ $getRoute('pelatihan') }}">
                    <span class="oi oi-star"></span>
                    Pelatihan
                </a>
            </li>
        @endif

        @if (in_array('berkala', $perms))
            <li>
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
            <li>
                <a href="{{ $getRoute('penghasilan') }}">
                    <span class="oi oi-dollar"></span>
                    Penghasilan
                </a>
            </li>
        @endif

        @if (in_array('gaji', $perms))
            <li>
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
            <li>
                <a href="{{ $getRoute('cuti') }}">
                    <span class="oi oi-inbox"></span>
                    Manajemen / Otorisasi Cuti
                </a>
            </li>
        @endif

        @if (in_array('ordercuti', $perms))
            <li>
                <a href="{{ $getRoute('ordercuti') }}">
                    <span class="oi oi-inbox"></span>
                    Permohonan Cuti
                </a>
            </li>
        @endif
    @endif

    {{-- MUTASI --}}
    @if (in_array('mutasi', $perms) || in_array('mutasipangkat', $perms))
        <div class="sidebar-section">MUTASI</div>

        @if (in_array('mutasi', $perms))
            <li>
                <a href="{{ $getRoute('mutasi') }}">
                    <span class="oi oi-transfer"></span>
                    Mutasi Jabatan
                </a>
            </li>
        @endif

        @if (in_array('mutasipangkat', $perms))
            <li>
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
            <li>
                <a href="{{ $getRoute('peraturan') }}">
                    <span class="oi oi-book"></span>
                    Peraturan
                </a>
            </li>
        @endif

        @if (in_array('categories', $perms))
            <li>
                <a href="{{ $getRoute('categories') }}">
                    <span class="oi oi-tags"></span>
                    Kategori Peraturan
                </a>
            </li>
        @endif

        @if (in_array('kepatuhan', $perms))
            <li>
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
            <li>
                <a href="{{ $getRoute('asisten_sikap') }}">
                    <span class="oi oi-chat"></span>
                    Asisten Sikap (AI)
                </a>
            </li>
        @endif

        @if (in_array('wa_setting', $perms))
            <li>
                <a href="{{ $getRoute('wa_setting') }}">
                    <span class="oi oi-cog"></span>
                    Pengaturan WA
                </a>
            </li>
        @endif

        @if (in_array('resetpassword', $perms))
            <li>
                <a href="{{ $getRoute('resetpassword') }}">
                    <span class="oi oi-key"></span>
                    Reset Password
                </a>
            </li>
        @endif
    @endif

</div>

