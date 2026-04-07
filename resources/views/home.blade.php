@extends('layouts.global')
@section("title") Home @endsection
@section('content')
    <link rel="stylesheet" href="{{ asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Sans:wght@400;500;600&display=swap">

    <style>
        :root {
            --primary-dark: rgb(32, 46, 120);
        }

        .home-shell {
            font-family: 'Plus Jakarta Sans', 'IBM Plex Sans', sans-serif;
            color: #0f172a;
        }

        /* HERO CARD */
        .hero-card {
            background: linear-gradient(140deg, #1e3a8a 100%);
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            color: #f8fafc;
        }

        .hero-title {
            font-size: 1.2rem;
            font-weight: 800;
        }

        .hero-subtitle {
            opacity: .95;
            font-size: .93rem;
        }

        /* PANEL CARD - Konsisten */
        .panel-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .panel-head {
            border: 0;
            padding: 0.9rem 1.25rem;
            font-weight: 700;
            font-size: .95rem;
            color: #fff;
            background-color: var(--primary-dark);
        }

        .panel-body {
            padding: 1rem;
            background: #ffffff;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* FILTER SECTION */
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: .5rem;
            margin-bottom: .8rem;
        }

        .filter-grid .form-control {
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            font-size: .8rem;
            /* Ukuran lebih kecil */
            height: 36px;
            /* Tinggi lebih pendek */
        }

        .filter-action {
            display: flex;
            gap: .4rem;
            margin-bottom: .8rem;
        }

        .filter-action .btn {
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            padding: .35rem .7rem;
        }

        /* TABLE MODERN - COMPACT & FIXED HEIGHT */
        .table-wrapper {
            max-height: 280px;
            /* Batas tinggi tabel agar rata */
            overflow-y: auto;
            /* Scroll internal */
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .table-modern {
            margin-bottom: 0;
            font-size: .8rem;
            /* Font lebih kecil */
        }

        .table-modern thead th {
            background-color: var(--primary-dark);
            color: #ffffff;
            font-weight: 700;
            border-bottom: none;
            padding: .6rem .5rem;
            /* Padding header dikurangi */
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table-modern tbody td {
            vertical-align: middle;
            padding: .5rem;
            /* Padding body dikurangi */
            border-color: #f1f5f9;
            color: #334155;
        }

        .table-modern tbody tr:nth-of-type(odd) {
            background-color: #f8fafc;
        }

        .table-modern tbody tr:hover {
            background-color: #eff6ff;
        }

        .table-modern .btn-detail {
            border-radius: 5px;
            font-size: .7rem;
            font-weight: 700;
            padding: .2rem .5rem;
            background: var(--primary-dark);
            color: #fff;
        }

        .table-modern .btn-detail:hover {
            opacity: 0.9;
            color: #fff;
        }

        .empty-state {
            text-align: center;
            color: #64748b;
            font-size: .85rem;
            padding: 2rem;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
        }

        /* ADMIN CARD - Samakan style */
        .admin-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .admin-head {
            background-color: var(--primary-dark);
            color: #fff;
            border-bottom: 0;
            font-weight: 700;
            padding: 0.9rem 1rem;
            font-size: .95rem;
        }
    </style>

    @php
        $role = auth()->user()->roles;
        $detailRouteName = null;
        if ($role === 'KADIV') {
            $detailRouteName = 'kadiv.showatur';
        } elseif ($role === 'USER') {
            $detailRouteName = 'staff.showatur';
        } elseif ($role === 'PINCAB') {
            $detailRouteName = 'pincab.showatur';
        } elseif ($role === 'SUPERVISOR') {
            $detailRouteName = 'supervisor.showatur';
        } elseif ($role === 'PATUH') {
            $detailRouteName = 'peraturan.show';
        } elseif ($role === 'DIRUT') {
            $detailRouteName = 'direksi.showatur';
        } elseif ($role === 'DIRBIS') {
            $detailRouteName = 'dirbis.showatur';
        }
    @endphp

    <div class="container-fluid home-shell py-3 py-md-4">
        <div class="card hero-card mb-4">
            <div class="card-body px-3 px-md-4 py-3">
                <div class="hero-title">Dashboard SIKAP</div>
                <p class="hero-subtitle mb-2">Selamat datang di Sistem Informasi Kepegawaian dan Peraturan</p>
                @if (session('status'))
                    <div class="alert alert-success py-2 px-3 mb-0" role="alert" style="border-radius: 10px;">
                        {{ session('status') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- INTERNAL SECTION -->
            <div class="col-lg-6 mb-4">
                <div class="card panel-card">
                    <div class="panel-head">Peraturan Internal</div>
                    <div class="panel-body">
                        <form method="GET">
                            <div class="filter-grid">
                                <select name="internal_jenis" class="form-control">
                                    <option value="">Semua Jenis</option>
                                    <option value="SK" {{ $internalJenis === 'SK' ? 'selected' : '' }}>SK</option>
                                    <option value="SE" {{ $internalJenis === 'SE' ? 'selected' : '' }}>SE</option>
                                </select>
                                <div></div> <!-- Dummy column for alignment -->
                            </div>
                            <div class="filter-action">
                                <button type="submit" class="btn btn-success">Terapkan</button>
                                <a href="{{ url('/home') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>

                        @if($internalPeraturan->count())
                            <div class="table-wrapper">
                                <table class="table table-bordered table-modern">
                                    <thead>
                                        <tr>
                                            <th>Nama Peraturan</th>
                                            <th>Jenis</th>
                                            <th>Tgl Input</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($internalPeraturan as $atur)
                                            <tr>
                                                <td>{{ $atur->name }}</td>
                                                <td>{{ $atur->jenis_surat ?: '-' }}</td>
                                                <td>{{ optional($atur->created_at)->format('d-m-Y') }}</td>
                                                <td class="text-center">
                                                    @if($detailRouteName && Route::has($detailRouteName))
                                                        <a href="{{ route($detailRouteName, $atur->id) }}"
                                                            class="btn btn-detail">Detail</a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">Belum ada data internal.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- EXTERNAL SECTION -->
            <div class="col-lg-6 mb-4">
                <div class="card panel-card">
                    <div class="panel-head">Peraturan External</div>
                    <div class="panel-body">
                        <form method="GET" id="formExternal">
                            <div class="filter-grid">
                                <select name="external_jenis" class="form-control" id="selectJenisExternal">
                                    <option value="">Semua Jenis</option>
                                    <option value="OJK" {{ $externalJenis === 'OJK' ? 'selected' : '' }}>OJK</option>
                                    <option value="LPS" {{ $externalJenis === 'LPS' ? 'selected' : '' }}>LPS</option>
                                    <option value="POJK" {{ $externalJenis === 'POJK' ? 'selected' : '' }}>POJK</option>
                                    <option value="SEOJK" {{ $externalJenis === 'SEOJK' ? 'selected' : '' }}>SEOJK</option>
                                    <option value="PADK" {{ $externalJenis === 'PADK' ? 'selected' : '' }}>PADK</option>
                                </select>

                                <!-- Sub Jenis OJK (Hanya muncul jika pilih OJK) -->
                                <div id="wrapperSubOjk" style="display: none;">
                                    <select name="external_sub_jenis" class="form-control">
                                        <option value="">Semua Sub OJK</option>
                                        <option value="POJK" {{ $externalSubJenis === 'POJK' ? 'selected' : '' }}>POJK
                                        </option>
                                        <option value="SEOJK" {{ $externalSubJenis === 'SEOJK' ? 'selected' : '' }}>SEOJK
                                        </option>
                                        <option value="PADK" {{ $externalSubJenis === 'PADK' ? 'selected' : '' }}>PADK
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="filter-action">
                                <button type="submit" class="btn btn-success">Terapkan</button>
                                <a href="{{ url('/home') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>

                        @if($externalPeraturan->count())
                            <div class="table-wrapper">
                                <table class="table table-bordered table-modern">
                                    <thead>
                                        <tr>
                                            <th>Nama Peraturan</th>
                                            <th>Jenis</th>
                                            <th>Detail Jenis</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($externalPeraturan as $atur)
                                            <tr>
                                                <td>{{ $atur->name }}</td>
                                                <td>{{ $atur->jenis_surat ?: '-' }}</td>
                                                <td>
                                                    @if($atur->jenis_surat == 'OJK')
                                                        {{ $atur->jenis_ojk ?: '-' }}
                                                    @elseif($atur->jenis_surat == 'LPS')
                                                        {{ $atur->jenis_lps ?: '-' }} <!-- Kolom baru LPS -->
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($detailRouteName && Route::has($detailRouteName))
                                                        <a href="{{ route($detailRouteName, $atur->id) }}"
                                                            class="btn btn-detail">Detail</a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">Belum ada data external.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ADMIN SECTION -->
        @if(auth()->user()->roles == 'ADMIN')
            <div class="row">
                <div class="col-xl-6 mb-4">
                    <div class="card admin-card h-100">
                        <div class="card-header admin-head text-center">Pangkat</div>
                        <div class="card-body p-0">
                            <div class="table-wrapper" style="max-height: 250px;">
                                <table class="table table-bordered table-modern" id="pangkat">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Cabang</th>
                                            <th>Tgl Kenaikan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 mb-4">
                    <div class="card admin-card h-100">
                        <div class="card-header admin-head text-center">Berkala</div>
                        <div class="card-body p-0">
                            <div class="table-wrapper" style="max-height: 250px;">
                                <table class="table table-bordered table-modern" id="berkala">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Cabang</th>
                                            <th>Tgl Kenaikan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            // 1. Logika Show/Hide Sub Jenis OJK
            function toggleSubOjk() {
                var val = $('#selectJenisExternal').val();
                if (val === 'OJK') {
                    $('#wrapperSubOjk').slideDown('fast');
                } else {
                    $('#wrapperSubOjk').slideUp('fast');
                    // Optional: Reset value sub jenis jika disembunyikan
                    $('#wrapperSubOjk select').val('');
                }
            }

            // Jalankan saat halaman load
            toggleSubOjk();
            // Jalankan saat ada perubahan pilihan
            $('#selectJenisExternal').on('change', toggleSubOjk);

            // 2. Datatables Setup
            if ($('#pangkat').length) {
                $('#pangkat').DataTable({
                    pageLength: 5,
                    processing: true,
                    scrollCollapse: true,
                    ajax: { url: '/Pegawai/ListPangkat' },
                    columns: [
                        { data: 'DT_RowIndex' },
                        { data: 'nama' },
                        { data: 'cabang' },
                        { data: 'jdpang' }
                    ]
                });
            }

            if ($('#berkala').length) {
                $('#berkala').DataTable({
                    pageLength: 5,
                    processing: true,
                    scrollCollapse: true,
                    ajax: { url: '/pegawai/data' },
                    columns: [
                        { data: 'DT_RowIndex' },
                        { data: 'nama' },
                        { data: 'cabang' },
                        { data: 'jdber' }
                    ]
                });
            }
        });
    </script>
@endsection