@extends('layouts.global')
@section('title') List Log Pegawai @endsection
@section('content')

	<div class="card">
		<div class="card-header">
			<h4 class="card-title">Filter Log</h4>
		</div>
		<div class="card-body">
			<form action="{{ route('kepatuhan.loguser') }}" method="GET" class="form-horizontal">
				<div class="row">
					<!-- Filter Nama -->
					<div class="col-md-3 mb-2">
						<label class="control-label">Nama Pegawai</label>
						<input type="text" class="form-control" placeholder="Cari Nama..."
							value="{{ Request::get('keyword') }}" name="keyword">
					</div>

					<!-- Filter Jenis -->
					<div class="col-md-2 mb-2">
						<label class="control-label">Jenis Aktivitas</label>
						<select name="jenis" class="form-control">
							<option value="">Semua Jenis</option>
							<option value="Akses" {{ Request::get('jenis') == 'Akses' ? 'selected' : '' }}>Akses</option>
							<option value="Print" {{ Request::get('jenis') == 'Print' ? 'selected' : '' }}>Print</option>
							<option value="Lihat Dokumen" {{ Request::get('jenis') == 'Lihat Dokumen' ? 'selected' : '' }}>
								Lihat Dokumen</option>
							<option value="Permintaan Data" {{ Request::get('jenis') == 'Permintaan Data' ? 'selected' : '' }}>Permintaan Data</option>
						</select>
					</div>

					<!-- Filter Bulan (Baru) -->
					<div class="col-md-2 mb-2">
						<label class="control-label">Filter Bulan</label>
						<input type="month" class="form-control" name="filter_month"
							value="{{ Request::get('filter_month') }}">
						<small class="text-muted">Format: YYYY-MM</small>
					</div>

					<!-- Filter Tanggal Kustom -->
					<div class="col-md-2 mb-2">
						<label class="control-label">Dari Tanggal</label>
						<input type="date" class="form-control" name="start_date" value="{{ Request::get('start_date') }}">
					</div>

					<div class="col-md-2 mb-2">
						<label class="control-label">Sampai Tanggal</label>
						<input type="date" class="form-control" name="end_date" value="{{ Request::get('end_date') }}">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-12">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-search"></i> Filter
						</button>
						<a href="{{ route('kepatuhan.loguser') }}" class="btn btn-secondary">
							<i class="fa fa-refresh"></i> Reset
						</a>
						<!-- Tombol Export Excel (Baru) -->
						<a href="{{ route('kepatuhan.loguser.export', Request::query()) }}"
							class="btn btn-success float-right">
							<i class="fa fa-file-excel-o"></i> Export Excel
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<br>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">Daftar Log Aktivitas</h4>
				</div>
				<div class="card-body">
					@if(session('status'))
						<div class="alert alert-warning">
							{{ session('status') }}
						</div>
					@endif

					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th><b>Nama Pegawai</b></th>
								<th><b>Jenis</b></th>
								<th><b>Keterangan</b></th>
								<th><b>Waktu Akses</b></th>
								<th><b>Mulai</b></th>
								<th><b>Selesai</b></th>
								<th><b>Durasi Aktif</b></th>
							</tr>
						</thead>
						<tbody>
							@php
								$no = $logs->firstItem() ?? 1;
							@endphp
							@forelse($logs as $log)
								<tr>
									<td>{{ $no++ }}</td>
									<td>{{ $log->nampeg ?? '-' }}</td>
									<td>
										<span class="badge badge-info">{{ $log->jenis ?? '-' }}</span>
									</td>
									<td>{{ $log->keterangan ?? '-' }}</td>
									<td>{{ $log->waktu_akses ? \Carbon\Carbon::parse($log->waktu_akses)->locale('id')->translatedFormat('d M Y, H:i') : '-' }}
									</td>
									<td>{{ $log->mulai ? \Carbon\Carbon::parse($log->mulai)->locale('id')->translatedFormat('d M Y, H:i') : '-' }}
									</td>
									<td>{{ $log->selesai ? \Carbon\Carbon::parse($log->selesai)->locale('id')->translatedFormat('d M Y, H:i') : '-' }}
									</td>
									<td>
										@if(!is_null($log->active_seconds))
											@php
												$minutes = floor(((int) $log->active_seconds) / 60);
												$seconds = ((int) $log->active_seconds) % 60;
											@endphp
											{{ $minutes }}m {{ $seconds }}s
										@else
											-
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center text-muted">Tidak ada data ditemukan.</td>
								</tr>
							@endforelse
						</tbody>
					</table>

					<!-- Pagination dengan Angka -->
					<div class="d-flex justify-content-center">
						{{ $logs->appends(Request::query())->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection