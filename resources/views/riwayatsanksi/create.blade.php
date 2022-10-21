@extends('layouts.global');
@section('title') Create Riwayat Pendidikan @endsection
@section('footer-scripts')
    <link href="\assets\select2\dist\css\select2.min.css" rel="stylesheet" />
    <script src="\assets\select2\dist\js\select2.min.js"></script>
    <script>
        $('#pegawai').select2({ajax: { url: '/ajax/pegawai/search',
                processResults: function(data){ return {results: data.map(function(item){return {id: item.id, text:item.name} })
                }
                }
            }
        });
    </script>
@endsection
@section('content')


    <div class="col-md-8">
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('riwayatsanksi.store')}}" method="POST">
            @csrf
            <label>Nama Pegawai</label>
            <select class="form-control select2" name="pegawai" id="pegawai">
            </select><br><br>
            <label>Jenis Sanksi</label>
            <select class="form-control" name="sanksi">
                <option value="#">Pilih Jenis Sanksi</option>
                @foreach($sanksi as $jenis => $name)
                    <option value="{{$jenis}}">{{$name}}</option>
                @endforeach
            </select>
            <br>
            <label>Tanggal Sanksi</label>
            <input type="date" class="form-control" name="tglsanksi"><br>
            <label>No. Sanksi</label>
            <input type="text" class="form-control" name="nosanksi"><br>
            <label>Keterangan</label>
            <textarea name="ket" id="ket" class="form-control" placeholder="Keterangan Sanksi"></textarea><br>

            <input type="submit" class="btn btn-primary" value="Save">
            <a href="{{route('pegawai.index')}}" class="btn btn-primary"> Back </a>
        </form>
    </div>
@endsection
