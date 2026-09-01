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

<div class="sidebar-menu">

    @php $perms = auth()->user()->menu_permissions ?? []; @endphp

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
                <a href="{{ route('users.index') }}">
                    <span class="oi oi-person"></span>
                    Users
                </a>
            </li>
        @endif

        @if (in_array('setuser', $perms))
            <li>
                <a href="{{ route('setuser.index') }}">
                    <span class="oi oi-lock-locked"></span>
                    Setup Otorisasi Cuti
                </a>
            </li>
        @endif

        @if (in_array('loguser', $perms))
            <li>
                <a href="{{ route('Loguser.index') }}">
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
                <a href="{{ route('pegawai.index') }}">
                    <span class="oi oi-people"></span>
                    Pegawai
                </a>
            </li>
        @endif

        @if (in_array('cabang', $perms))
            <li>
                <a href="{{ route('cabang.index') }}">
                    <span class="oi oi-map-marker"></span>
                    Kantor
                </a>
            </li>
        @endif

        @if (in_array('jabatan', $perms))
            <li>
                <a href="{{ route('jabatan.index') }}">
                    <span class="oi oi-briefcase"></span>
                    Jabatan
                </a>
            </li>
        @endif

        @if (in_array('pangkat', $perms))
            <li>
                <a href="{{ route('pangkat.index') }}">
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
                <a href="{{ route('keluarga.index') }}">
                    <span class="oi oi-heart"></span>
                    Data Keluarga
                </a>
            </li>
        @endif

        @if (in_array('pelatihan', $perms))
            <li>
                <a href="{{ route('pelatihan.index') }}">
                    <span class="oi oi-star"></span>
                    Pelatihan
                </a>
            </li>
        @endif

        @if (in_array('berkala', $perms))
            <li>
                <a href="{{ route('pegawai.listberkala') }}">
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
                <a href="{{ route('penghasilan.index') }}">
                    <span class="oi oi-dollar"></span>
                    Penghasilan
                </a>
            </li>
        @endif

        @if (in_array('gaji', $perms))
            <li>
                <a href="{{ route('gaji.index') }}">
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
                <a href="{{ route('cuti.index') }}">
                    <span class="oi oi-inbox"></span>
                    Manajemen Cuti
                </a>
            </li>
        @endif

        @if (in_array('ordercuti', $perms))
            <li>
                <a href="{{ route('ordercuti.indexcuti') }}">
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
                <a href="{{ route('mutasi.index') }}">
                    <span class="oi oi-transfer"></span>
                    Mutasi Jabatan
                </a>
            </li>
        @endif

        @if (in_array('mutasipangkat', $perms))
            <li>
                <a href="{{ route('mutasipangkat.index') }}">
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
                <a href="{{ route('peraturan.index') }}">
                    <span class="oi oi-book"></span>
                    Peraturan
                </a>
            </li>
        @endif

        @if (in_array('categories', $perms))
            <li>
                <a href="{{ route('categories.index') }}">
                    <span class="oi oi-tags"></span>
                    Kategori Peraturan
                </a>
            </li>
        @endif

        @if (in_array('kepatuhan', $perms))
            <li>
                <a href="{{ route('kepatuhan.statusatur') }}">
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
                <a href="{{ route('admin.asisten-sikap.index') }}">
                    <span class="oi oi-chat"></span>
                    Asisten Sikap (AI)
                </a>
            </li>
        @endif

        @if (in_array('wa_setting', $perms))
            <li>
                <a href="{{ route('admin.wa-setting.index') }}">
                    <span class="oi oi-cog"></span>
                    Pengaturan WA
                </a>
            </li>
        @endif

        @if (in_array('resetpassword', $perms))
            <li>
                <a href="{{ route('resetpassword.index') }}">
                    <span class="oi oi-key"></span>
                    Reset Password
                </a>
            </li>
        @endif
    @endif

</div>
