@extends('layouts.global')
@section('title') List Data Riwayat Pekerjaan @endsection

@section('content')

    <div class="row">

        <div class="col-md-6">
            <form action="{{route('riwayatsanksi.list',[$pegawai['id']])}}">

            </form>
        </div>
    </div>
    <hr class="my-3">
    <div class="row">
        <div class="col-md-12">
            @if(session('status'))
                <div class="alert alert-success">
                    {{session('status')}}
                </div>
            @endif

            <div class="row mb-3">

                <div class="col-md-12 text-right">
                    <a href="{{route('riwayatsanksi.tambah',$pegawai['id'])}}" class="btn btn-primary">Tambah Data sanksi</a>
                </div>
            </div>
            <b>Data Riwayat Sanksi {{$pegawai['name']}}</b><br>
            <table class="table table-bordered table-stripped">
                <thead>
                <tr align="center">

                    <th><b>Jenis Sanksi</b></th>
                    <th><b>Tanggal Sanksi</b></th>
                    <th><b>Nomor Sanksi</b></th>
                    <th><b>Keterangan</b></th>
                    <th><b>Action</b></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    @foreach ($datasanksi as $riwayatsanksi)
                        <td>{{$riwayatsanksi['sanksipeg']}}</td>
                        <td>{{$riwayatsanksi['tglsanksi']}}</td>
                        <td>{{$riwayatsanksi['nosanksi']}}</td>
                        <td>{{$riwayatsanksi['ket']}}</td>

                        <td><a href="{{route('riwayatsanksi.edit',[$riwayatsanksi['id']])}}" class="btn btn-info btn-sm"> Edit </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete Permanent?')" action="{{route('riwayatsanksi.delete-permanent',[$riwayatsanksi['id']])}}">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="submit" value="Delete" class="btn btn-danger btn-sm">
                            </form><br>
                        </td>
                </tr>


                @endforeach
                </tbody>
            </table>
@endsection
