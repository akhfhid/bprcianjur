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
</style>
<div class="sidebar-menu">

    <div class="sidebar-section">MAIN</div>
    <li>
        <a href="/home">
            <span class="oi oi-dashboard"></span>
            Dashboard
        </a>
    </li>

    <div class="sidebar-section">USER & AKSES</div>
    <li>
        <a href="{{ route('users.index') }}">
            <span class="oi oi-person"></span>
            Users
        </a>
    </li>

    <div class="sidebar-section">MASTER DATA</div>
    <li>
        <a href="{{ route('pegawai.index') }}">
            <span class="oi oi-people"></span>
            Pegawai
        </a>
    </li>
    <li>
        <a href="{{ route('cabang.index') }}">
            <span class="oi oi-map-marker"></span>
            Kantor
        </a>
    </li>
    <li>
        <a href="{{ route('jabatan.index') }}">
            <span class="oi oi-briefcase"></span>
            Jabatan
        </a>
    </li>
    <li>
        <a href="{{ route('pangkat.index') }}">
            <span class="oi oi-chevron-top"></span>
            Pangkat
        </a>
    </li>

    <div class="sidebar-section">CUTI</div>
    <li>
        <a href="{{ route('cuti.index') }}">
            {{-- <span class="fas fa-folder-open"></span> --}}
            <span class="fa-solid fa-screwdriver-wrench"></span>
            Manajemen Cuti
        </a>
    </li>
    <li><a href="{{ route('ordercuti.indexcuti') }}">
            <span class=" oi oi-inbox"></span>
            Approve Cuti
        </a>
    </li>

    <li>
        <a href="{{ route('setuser.index') }}">
            <span class="oi oi-lock-locked"></span>
           Setup Otorisasi Cuti
        </a>
    </li>
    <div class="sidebar-section">KEUANGAN</div>
    <li>
        <a href="{{ route('penghasilan.index') }}">
            <span class="oi oi-dollar"></span>
            Gaji
        </a>
    </li>
    <li>
        <a href="{{ route('pegawai.listberkala') }}">
            <span class="oi oi-timer"></span>
            Kepangkatan
        </a>
    </li>

</div>
