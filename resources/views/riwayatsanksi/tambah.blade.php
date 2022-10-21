@extends ('layouts.global')
@section('title')Detail Data Pegawai @endsection

@section('content')

    <div class="col-md-8">
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatsanksi.simpan')}}" method="POST">
            @csrf
            <label>Nama Pegawai</label>
            <input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
            <input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>

            <label>Jenis Sanksi</label>
            <select class="form-control" name="sanksi">
                @foreach ($sanksi as $jsanksi =>$name)
                    <option value="{{$jsanksi}}">{{$name}}</option>
                @endforeach
            </select><br>
            <label>Tanggal Sanksi</label>
            <input type="date" name="tglsanksi" class="form-control"><br>

            <label>No. Sanksi</label>
            <input type="text" class="form-control" name="nomor"><br>

            <label>Keterangan</label>
            <textarea name="ket" class="form-control" id="ket" placeholder="Keterangan Sanksi"></textarea><br>

            <input type="submit" class="btn btn-primary" value="Save">
            <a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
        </form>
    </div>
    @endsection
