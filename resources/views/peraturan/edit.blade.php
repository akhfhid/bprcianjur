@extends('layouts.global')
@section('title')
    Edit Data Peraturan
@endsection
@section('footer-scripts')
    <script src="{{ asset('polished\summernote\summernote-bs4.js') }}"></script>
    <script>
        $(function() {
            //summernote
            $('#textarea').summernote()
        });
    </script>
@endsection

@section('content')
    Edit Data {{ $peraturan->name }}
    <hr class="my-3">

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="col-md-12">
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
            action="{{ route('peraturan.simpanedit', [$peraturan->id]) }}" method="POST">
            @csrf

            <label>Nama Peraturan</label><br>
            <input type="text" class="form-control" name="name" value="{{ $peraturan->name }}">
            <br>
            <br>
            <label>Kategori Peraturan</label>
            <select name="kategori" class="form-control" required>
                <option value="internal" {{ $peraturan->kategori == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="external" {{ $peraturan->kategori == 'external' ? 'selected' : '' }}>External</option>
            </select>
            <br>

            <label>Jenis Surat</label>
            <select name="jenis_surat" class="form-control" required>
                <option value="SK" {{ $peraturan->jenis_surat == 'SK' ? 'selected' : '' }}>Surat Keputusan (SK)</option>
                <option value="SE" {{ $peraturan->jenis_surat == 'SE' ? 'selected' : '' }}>Surat Edaran (SE)</option>
            </select>
            <br>
            <label>Nomor Surat Keputusan</label><br>
            <input type="text" class="form-control" name="nosk" value="{{ $peraturan->nosk }}">
            <br>
            <label>Tanggal Surat Keputusan</label>
            <input type="date" class="form-control" name="tglsk" value="{{ $peraturan->tglsk }}">
            <br>
            <label>Tanggal Berlaku Surat Keputusan</label>
            <input type="date" class="form-control" name="tgllaku" value="{{ $peraturan->tgllaku }}">
            <br>
            <label>Uraian</label>
            <textarea class="form-control" name="uraian" placeholder="Uraian Isi Peraturan" align="justify"
                value="{{ $peraturan->uraian }}">{{ $peraturan->uraian }}</textarea>
            <br>
            <label>Lampiran Peraturan</label>
            <label>Upload PDF</label>
            <input type="file" accept="application/pdf" name="pdf" id="pdf">
            <textarea class="form-control" id="textarea" name="description"
                style="width: 100%; height: 500px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                height="500px"> {{ $peraturan->pdf }}</textarea>
    </div>
    <br>
    <input type="submit" class="btn btn-primary" value="Save">

    </form>
    </div>
@endsection
