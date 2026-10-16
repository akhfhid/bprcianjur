@extends('layouts.global')
@section('title')Rekap Cuti Pegawai @endsection
@section('content')

<div class="row">
<div class="col-md-12">

<h3><span class="oi oi-list"></span> Rekap Pemakaian Cuti Pegawai</h3>
<p class="text-muted">Rekap sisa dan pemakaian cuti seluruh pegawai di bawah otorisasi Anda (langsung maupun sebagai penerima kabotan).</p>

{{-- Kartu ringkasan --}}
<div class="row mb-3">
 <div class="col-md-4">
 <div class="card border-primary shadow-sm">
 <div class="card-body text-center">
 <h5 class="text-primary mb-0">{{ $data ? count($data) : 0 }}</h5>
 <small class="text-muted">Pegawai di bawah otorisasi</small>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card border-success shadow-sm">
 <div class="card-body text-center">
 <h5 class="text-success mb-0">{{ $totalScuti }} hari</h5>
 <small class="text-muted">Total sisa cuti (belum diambil)</small>
 </div>
 </div>
 </div>
 <div class="col-md-4">
 <div class="card border-warning shadow-sm">
 <div class="card-body text-center">
 <h5 class="text-warning mb-0">{{ $totalPakai }} hari</h5>
 <small class="text-muted">Total cuti terpakai tahun {{ $tahun }}</small>
 </div>
 </div>
 </div>
</div>

{{-- Filter --}}
<div class="card shadow-sm mb-3">
 <div class="card-body py-3">
 <form action="{{route('kadiv.rekapcuti')}}" method="GET">
 <div class="row align-items-end">
 <div class="col-md-4">
 <label class="mb-1"><b>Cari Nama Pegawai</b></label>
 <input value="{{ $name }}" name="name" type="text" class="form-control" placeholder="Ketik nama pegawai...">
 </div>
 <div class="col-md-3">
 <label class="mb-1"><b>Tahun</b></label>
 <select name="tahun" class="form-control">
 @foreach($tahunList as $th)
 <option value="{{ $th }}" {{ $th == $tahun ? 'selected' : '' }}>{{ $th }}</option>
 @endforeach
 </select>
 </div>
 <div class="col-md-3">
 <label class="mb-1"><b>Status Cuti</b></label>
 <select name="status" class="form-control">
 <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
 <option value="DISETUJUI" {{ $status == 'DISETUJUI' ? 'selected' : '' }}>Hanya Disetujui</option>
 <option value="SUBMIT" {{ $status == 'SUBMIT' ? 'selected' : '' }}>Menunggu Persetujuan</option>
 <option value="DITOLAK" {{ $status == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
 </select>
 </div>
 <div class="col-md-2">
 <button type="submit" class="btn btn-primary btn-block"><span class="oi oi-magnifying-glass"></span> Tampilkan</button>
 </div>
 </div>
 </form>
 </div>
</div>

{{-- Tabel rekap --}}
<div class="card shadow-sm">
 <div class="card-body">
 <div class="table-responsive">
 <table class="table table-striped table-bordered table-hover" id="tabelRekap">
 <thead class="thead-dark">
 <tr align="center">
 <th rowspan="2" style="vertical-align:middle"><b>No</b></th>
 <th rowspan="2" style="vertical-align:middle"><b>Nama Pegawai</b></th>
 <th rowspan="2" style="vertical-align:middle"><b>Jabatan</b></th>
 <th rowspan="2" style="vertical-align:middle"><b>Sisa Cuti</b></th>
 <th colspan="4" class="text-center"><b>Cuti Terpakai {{ $tahun }}</b></th>
 </tr>
 <tr align="center" class="bg-light">
 <th>Tahunan</th>
 <th>Wajib</th>
 <th>Lainnya</th>
 <th>Total</th>
 </tr>
 </thead>
 <tbody>
 @if(count($data) == 0)
 <tr>
 <td colspan="8" align="center" class="text-muted py-4">
 <span class="oi oi-info"></span> Tidak ada pegawai yang cocok dengan filter.
 </td>
 </tr>
 @else
 @foreach($data as $i => $row)
 <tr>
 <td align="center">{{ $i + 1 }}</td>
 <td><b>{{ $row['namapeg'] }}</b></td>
 <td>{{ $row['jabatan'] }}</td>
 <td align="center">
 <span class="badge badge-{{ $row['scuti'] <= 2 ? 'danger' : ($row['scuti'] <= 4 ? 'warning' : 'success') }} p-2" style="font-size:0.95em">
 {{ $row['scuti'] }} hari
 </span>
 </td>
 <td align="center">{{ $row['cutiTahunan'] }}</td>
 <td align="center">{{ $row['cutiWajib'] }}</td>
 <td align="center">{{ $row['cutiLainnya'] }}</td>
 <td align="center"><b>{{ $row['total'] }}</b></td>
 </tr>
 @endforeach
 @endif
 </tbody>
 @if(count($data) > 0)
 <tfoot class="font-weight-bold bg-light">
 <tr>
 <td colspan="3" align="right">TOTAL</td>
 <td align="center">{{ $totalScuti }}</td>
 <td align="center">{{ collect($data)->sum('cutiTahunan') }}</td>
 <td align="center">{{ collect($data)->sum('cutiWajib') }}</td>
 <td align="center">{{ collect($data)->sum('cutiLainnya') }}</td>
 <td align="center">{{ $totalPakai }}</td>
 </tr>
 </tfoot>
 @endif
 </table>
 </div>
 <p class="text-muted mb-0 mt-2">
 <small><span class="oi oi-info"></span> Catatan: "Sisa Cuti" diambil dari saldo cuti pegawai saat ini.
 Jika status difilter bukan "Semua Status", kolom cuti terpakai hanya menghitung cuti dengan status tersebut.</small>
 </p>
 </div>
</div>

<div class="mt-3">
 <a href="{{route('kadiv.cutiindex')}}" class="btn btn-secondary"><span class="oi oi-arrow-left"></span> Kembali</a>
</div>

</div>
</div>
@endsection
