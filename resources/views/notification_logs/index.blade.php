@extends('layouts.global')

@section('title')
Notification Log
@endsection

@section('pageTitle')
Notification Log
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2 col-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Total</small>
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Success</small>
                    <h4 class="mb-0 text-success">{{ $stats['success'] }}</h4>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Error</small>
                    <h4 class="mb-0 text-danger">{{ $stats['error'] }}</h4>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Kategori Peraturan</small>
                    <h4 class="mb-0">{{ $stats['peraturan'] }}</h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="border rounded p-3 h-100">
                    <small class="text-muted d-block">Kategori Cuti</small>
                    <h4 class="mb-0">{{ $stats['cuti'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('notification-logs.index') }}">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label>Kategori</label>
                    <select class="form-control" name="category">
                        <option value="">Semua</option>
                        <option value="peraturan" {{ request('category') == 'peraturan' ? 'selected' : '' }}>Peraturan</option>
                        <option value="cuti" {{ request('category') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="">Semua</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Channel</label>
                    <select class="form-control" name="channel">
                        <option value="">Semua</option>
                        <option value="email" {{ request('channel') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="wa" {{ request('channel') == 'wa' ? 'selected' : '' }}>WA</option>
                        <option value="system" {{ request('channel') == 'system' ? 'selected' : '' }}>System</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Dari Tanggal</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="form-group col-md-2">
                    <label>Sampai Tanggal</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="form-group col-md-2">
                    <label>Kata Kunci</label>
                    <input type="text" class="form-control" name="keyword" placeholder="nama/email/ref" value="{{ request('keyword') }}">
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('notification-logs.index') }}" class="btn btn-light border">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="min-width: 155px;">Waktu</th>
                        <th>Kategori</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Referensi</th>
                        <th>Penerima</th>
                        <th style="min-width: 220px;">Success Detail</th>
                        <th style="min-width: 260px;">Error Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if ($log->category === 'peraturan')
                                    <span class="badge badge-info">Peraturan</span>
                                @elseif ($log->category === 'cuti')
                                    <span class="badge badge-secondary">Cuti</span>
                                @else
                                    <span class="badge badge-light">{{ strtoupper($log->category) }}</span>
                                @endif
                            </td>
                            <td>{{ strtoupper($log->channel ?? '-') }}</td>
                            <td>
                                @if ($log->status === 'success')
                                    <span class="badge badge-success">SUCCESS</span>
                                @else
                                    <span class="badge badge-danger">ERROR</span>
                                @endif
                            </td>
                            <td>
                                <div><strong>{{ $log->reference_type ?? '-' }}</strong></div>
                                <small>ID: {{ $log->reference_id ?? '-' }}</small>
                                <br>
                                <small>Cabang: {{ $log->cabang_id ?? '-' }}</small>
                            </td>
                            <td>
                                <div>{{ $log->recipient_name ?? '-' }}</div>
                                <small>{{ $log->recipient_email ?? '-' }}</small>
                                <br>
                                <small>{{ $log->recipient_phone ?? '-' }}</small>
                            </td>
                            <td>
                                <div><strong>{{ $log->subject ?? '-' }}</strong></div>
                                <small>{{ $log->message ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-danger">{{ $log->error_message ?? '-' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data notification log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $logs->links() }}
    </div>
</div>
@endsection

