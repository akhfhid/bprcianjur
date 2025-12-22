<table class="table table-hover mb-0 text-center">
    <thead>
        <tr>
            <th class="text-left">Pegawai & Kantor</th>
            <th>Jenis</th>
            <th>Durasi</th>
            <th>Periode</th>
            <th>Alasan</th>
            <th>Status</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orderc as $order)
        <tr>
            <td class="text-left">
                <b>{{ $order['namapeg'] }}</b><br>
                <small class="text-muted">{{ $order['namacab'] }}</small>
            </td>
            <td>{{ $order['jenis'] }}</td>
            <td><b>{{ $order['jmlcuti'] }}</b> Hari</td>
            <td>
                <small>{{ $order['tglawal'] }}</small><br>
                <small class="text-muted">s/d</small><br>
                <small>{{ $order['tglakhir'] }}</small>
            </td>
            <td class="text-truncate" style="max-width:140px">
                {{ $order['alasan'] }}
            </td>
            <td>
                @php
                    $cls='bg-submit';
                    if($order['status']=='DISETUJUI')$cls='bg-disetujui';
                    if($order['status']=='DITOLAK')$cls='bg-ditolak';
                @endphp
                <span class="badge-status {{ $cls }}">{{ $order['status'] }}</span>
            </td>
            <td>
                @if($order['statdiket']=='DISETUJUI' && $order['status']=='SUBMIT')
                <form method="POST" action="{{ route('ordercuti.setuju',$order['id']) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm">Setuju</button>
                </form>
                <form method="POST" action="{{ route('ordercuti.tolak',$order['id']) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-danger btn-sm">Tolak</button>
                </form>
                @else
                    <small class="text-muted">Selesai</small>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="py-5 text-muted">Data tidak ditemukan</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($ordercuti->hasPages())
<div class="p-3 border-top bg-light text-center">
    {{ $ordercuti->links() }}
</div>
@endif
