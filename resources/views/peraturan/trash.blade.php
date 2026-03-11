@extends('layouts.global')

@section('title') Data Trash Peraturan @endsection

@section('content')

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Inter', sans-serif;
			background-color: #f8f9fc;
			color: #333;
		}

		.card {
			border: none;
			border-radius: 8px;
			box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
		}

		.page-title {
			font-size: 1.5rem;
			font-weight: 700;
			color: #2d3748;
			margin-bottom: 5px;
		}

		.page-subtitle {
			font-size: .875rem;
			color: #8a94a6;
			margin-bottom: 1.5rem;
		}

		.table thead th {
			background-color: #f8f9fc;
			color: #6c757d;
			font-weight: 700;
			font-size: .75rem;
			text-transform: uppercase;
			letter-spacing: .5px;
			border-bottom: 2px solid #e3e6f0;
			padding: 1rem;
		}

		.table tbody td {
			padding: 1rem;
			vertical-align: middle;
			font-size: .9rem;
			border-bottom: 1px solid #e3e6f0;
		}

		.nav-pills .nav-link {
			border-radius: 50px;
			padding: 0.5rem 1.2rem;
			font-weight: 500;
			font-size: 0.875rem;
		}

		.nav-pills .nav-link.active {
			background-color: #e74a3b;
			/* Warna merah untuk trash */
			box-shadow: 0 4px 6px rgba(231, 74, 59, 0.2);
		}
	</style>

	<div class="container-fluid py-4">

		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<div>
				<h1 class="page-title mb-0 text-danger"><i class="fas fa-trash-alt mr-2"></i> Trash Peraturan</h1>
				<p class="page-subtitle mb-0 d-none d-sm-block">Data dokumen peraturan yang telah dihapus sementara.</p>
			</div>
			<div>
				<ul class="nav nav-pills">
					<li class="nav-item mr-2">
						@if(auth()->user()->roles == 'KADIV')
							<a class="nav-link bg-white text-dark shadow-sm border" href="{{route('kadiv.peraturan')}}">
								<i class="fas fa-file-alt mr-1"></i> Published
							</a>
						@elseif(auth()->user()->roles == 'PATUH')
							<a class="nav-link bg-white text-dark shadow-sm border" href="{{route('peraturan.index')}}">
								<i class="fas fa-file-alt mr-1"></i> Published
							</a>
						@endif
					</li>
					<li class="nav-item">
						<a class="nav-link active" href="{{route('peraturan.trash')}}">
							<i class="fas fa-trash mr-1"></i> Trash
						</a>
					</li>
				</ul>
			</div>
		</div>

		@if(session('status'))
			<div class="alert alert-success shadow-sm">
				<i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
			</div>
		@endif

		<div class="card shadow-sm mb-4">
			<div
				class="card-header py-3 bg-white d-flex flex-column flex-md-row justify-content-between align-items-center">
				<h6 class="m-0 font-weight-bold text-dark mb-3 mb-md-0">Daftar Tempat Sampah</h6>

				<form action="{{route('peraturan.trash')}}" class="form-inline">
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
						</div>
						<input type="text" class="form-control border-left-0" placeholder="Cari Nama Peraturan..."
							value="{{Request::get('name')}}" name="name" style="width: 250px;">
						<div class="input-group-append">
							<button class="btn btn-outline-secondary" type="submit">Filter</button>
						</div>
					</div>
				</form>
			</div>

			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead>
							<tr>
								<th>Informasi Peraturan</th>
								<th>Tanggal SK</th>
								<th>Tanggal Masa Berlaku</th>
								<th>Waktu Penghapusan</th>
								<th class="text-center">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($peraturan as $atur)
								<tr>
									<td>
										<div class="font-weight-bold text-dark">{{$atur->name}}</div>
										<small class="text-muted"><i class="fas fa-hashtag mr-1"></i> {{$atur->nosk}}</small>
									</td>
									<td>{{ \Carbon\Carbon::parse($atur->tglsk)->translatedFormat('d F Y') }}</td>
									<td>{{ \Carbon\Carbon::parse($atur->tgllaku)->translatedFormat('d F Y') }}</td>
									<td>
										<div class="text-danger font-weight-bold">
											<i class="far fa-clock mr-1"></i>
											{{ \Carbon\Carbon::parse($atur->deleted_at)->translatedFormat('d F Y, H:i') }}
										</div>
										<small class="text-muted">
											<i class="fas fa-user-times mr-1"></i> Oleh:
											{{ $atur->deleted_by ?? 'Tidak diketahui' }}
										</small>
									</td>
									<td class="text-center">
										<div class="btn-group" role="group">
											<a href="{{route('peraturan.showtrash', [$atur->id])}}" class="btn btn-info btn-sm"
												data-toggle="tooltip" title="Detail">
												<i class="fas fa-eye"></i>
											</a>

											@if(auth()->user()->roles == 'PATUH')
												<a href="{{route('peraturan.restore', [$atur->id])}}" class="btn btn-success btn-sm"
													data-toggle="tooltip" title="Restore">
													<i class="fas fa-undo"></i>
												</a>

												<form class="d-inline" action="{{route('peraturan.delete-permanent', [$atur->id])}}"
													method="POST"
													onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen? Data yang dihapus permanen tidak dapat dikembalikan.')">
													@csrf
													<input type="hidden" name="_method" value="DELETE">
													<button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
														title="Delete Permanen">
														<i class="fas fa-trash-alt"></i>
													</button>
												</form>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="text-center py-5">
										<div class="text-muted">
											<i class="fas fa-box-open fa-3x mb-3 text-gray-300"></i>
											<p class="mb-0 font-weight-bold">Tempat sampah kosong.</p>
											<p class="small">Tidak ada data peraturan yang dihapus saat ini.</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>

			@if($peraturan->hasPages())
				<div class="card-footer bg-white border-top-0 pt-3">
					{{$peraturan->appends(Request::all())->links()}}
				</div>
			@endif
		</div>

	</div>

	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>

@endsection