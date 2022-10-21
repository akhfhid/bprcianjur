@extends('layouts.global')
@section('title') Kepegawaian @endsection

@section('content')


    <div class="col-md-8">
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatangkat.update',[$riwayatangkat->id])}}" method="POST">
            @csrf
            <label>Nama Pegawai</label>
            <input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
            <input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

            <label>Status Kepegawaian</label>
            <select class="form-control" name="speg">
                <option value="{{$spegawai->id}}">{{$spegawai->name}}</option>
                @foreach ($statuspegawai as $status =>$name)
                    <option value="{{$status}}">{{$name}}</option>
                @endforeach
            </select><br>
            <label>Terhitung Mulai Tanggal</label>
            <input type="date" name="tglangkat" class="form-control" value="{{$riwayatangkat->tglangkat}}"><br>
            <label>Nomor SK Pengangkatan</label>
	        <input type="text" name="nosk" class="form-control" value="{{$riwayatangkat->nosk}}"><br>
            <input type="submit" class="btn btn-primary" value="Save">
            <a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
        </form>
    </div>


@endsection
