@extends('layouts.global');
@section('title') Edit Riwayat Sanksi @endsection
@section('content')


    <div class="col-md-8">
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatsanksi.update',[$riwayat->id])}}}" method="POST">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <label>Nama Pegawai</label>
            <input type="text" class="form-control" value="{{$pegawai->name}}" name="namepeg" placeholder="Nama Pegawai" disabled="disabled">
            <input type="hidden" name="idpeg" value="{{$pegawai->id}}"><br>
            <in
            <label>Jenis Sanksi</label>
            <select class="form-control" name="sanksi">
                <option value="{{$riwayat->sanksi}}">{{$nama}}</option>
                @foreach($jenis as $sanksi => $name)
                    <option value="{{$sanksi}}">{{$name}}</option>
                @endforeach
            </select>
            <br>
            <label>Tanggal Sanksi</label>
            <input type="date" class="form-control" name="tglsanksi" value="{{$riwayat->tglsanksi}}"><br>
            <label>No. Sanksi</label>
            <input type="text" class="form-control" name="nosanksi" value="{{$riwayat->nosanksi}}"><br>
            <label>Keterangan</label>
            <textarea name="ket" id="ket" class="form-control" >{{$riwayat->ket}}</textarea><br>

            <input type="submit" class="btn btn-primary" value="Save">
            <a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
        </form>
    </div>
@endsection
<script>
    import Inactive_regions from "../../../public/jqvmap/examples/inactive_regions.html";
    export default {
        components: {Inactive_regions}
    }
</script>
