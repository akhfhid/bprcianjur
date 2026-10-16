@extends('layouts.global')
@section('title')Cuti Saya @endsection
@section('content')

 <div class="row">
 <div class="col-md-12">
@if(session('status'))
<div class="alert alert-success">
{{session('status')}}
</div>

@endif
<hr class="my-3">
<div class="row mb-3">
 <div class="col-md-12 text-right">
 <a href="{{route('supervisor.permohonancuti')}}" class="btn btn-primary">Permohonan Cuti Tahunan</a>
 <a href="{{route('supervisor.cutiwajib')}}" class="btn btn-primary">Permohonan Cuti Wajib</a>
 <a href="{{route('supervisor.cutilainnya')}}" class="btn btn-primary">Permohonan Cuti Lainnya</a>
 </div>
</div>
<div class="alert alert-info">
 <b>Sisa Cuti Belum Diambil:</b> {{ $sisacuti }} hari
</div>
<table class="table table-stripped table-bordered">
 <thead>
 <tr align="center">

 <th><b>Nama Pegawai</b></th>
 <th><b>Kantor</b></th>
 <th><b>Jenis Cuti</b></th>
 <th><b>Jumlah Cuti</b></th>
 <th><b>Tanggal Awal Cuti</b></th>
 <th><b>Tanggal Akhir Cuti</b></th>
 <th><b>Alasan Cuti</b></th>
 <th><b>Tanggal Mohon</b></th>
 <th><b>Status</b></th>
 </tr>
 </thead>
 <tbody>
 @foreach($orderc as $order)
 <tr>

 <td>{{$order['namapeg']}}</td>
 <td>{{$order['namacab']}}</td>
 <td>{{$order['jenis'] ?? '-'}}</td>
 <td>{{$order['jmlcuti']}}</td>
 <td>{{$order['tglawal']}}</td>
 <td>{{$order['tglakhir']}}</td>
 <td>{{$order['alasan']}}</td>
 <td>{{$order['tglmohon']}}</td>
 <td>
 @if($order['status'] =="SUBMIT")
 <span class="badge bg-warning text-light">{{$order['status']}}</span>
 @elseif($order['status'] =="DISETUJUI")
 <span class="badge bg-success text-light">{{$order['status']}}</span>
 @elseif($order['status'] =="DITOLAK")
 <span class="badge bg-info text-light">{{$order['status']}}</span>
 @elseif($order['status'] =="DIBATALKAN")
 <span class="badge bg-dark text-light">{{$order['status']}}</span>
 @endif
 </td>

 </td>
 </tr>
 @endforeach
 </tbody>
</table>
</div>
</div>
@endsection
