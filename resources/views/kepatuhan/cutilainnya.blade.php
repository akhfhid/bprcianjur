@extends('layouts.global')
@section('title')Permohonan Cuti @endsection
@section('content')

    <div class="row">

        <div class="col-md-8">
            <h3 align="center">Form Permohonan Cuti Lainnya Pegawai</h3>
            <hr class="my-3">
            @if(session('status'))
                <div class="alert alert-success">
                    {{session('status')}}
                </div>
            @endif
            <form action="{{route('kepatuhan.mintacuti')}}" method="POST" enctype="multipart/form-data" class="shadow-sm p-3 bg-white">
                @csrf

                <label>Nama Pegawai</label><br>
                <input type="text" value="{{$pegawai->name}}" name="name" class="form-control" disabled="disabled">
                <input type="hidden" value="{{$pegawai->id}}" name="idpeg" class="form-control">
                <br>
                <label>Tanggal Awal Cuti</label>
                <input type="date" class="form-control" name="tglawal">
                <br>
                <label>Tanggal Akhir Cuti</label>
                <input type="date" class="form-control" name="tglakhir">
                <br>
                <label>Alasan Cuti</label>
                <select name="alasan" class="form-control">
                    <option>Menikah</option>
                    <option>Menikahkan Anak</option>
                    <option>Mengkhitankan Anak</option>
                    <option>Istri Melahirkan</option>
                    <option>Istri/Suami Meninggal</option>
                    <option>Anak Meninggal</option>
                    <option>Orang Tua Meninggal</option>
                    <option>Mertua Meninggal</option>
                    <option>Menantu Meninggal</option>
                    <option>Anggota keluarga yang tinggal dalam satu rumah meninggal dunia</option>
                    </select>
                <br>
                <input type="hidden" class="form-control" name="jeniscuti" value="Cuti Lainnya">
                <input type="submit" class="btn btn-primary" value="Save">
            </form>

@endsection
