@extends('layouts.global')
@section('title')
    Edit Data Peraturan
@endsection

@section('footer-scripts')

    <script src="{{ asset('polished/summernote/summernote-bs4.js') }}"></script>

    <script>
        $(function () {

            // summernote
            $('#textarea').summernote();

            function loadJenisSurat() {

                let kategori = $('#kategori').val();
                let jenis = $('#jenis_surat');
                let selected = "{{ $peraturan->jenis_surat }}";

                jenis.empty();

                if (kategori === 'internal') {
                    jenis.append('<option value="SK">Surat Keputusan (SK)</option>');
                    jenis.append('<option value="SE">Surat Edaran (SE)</option>');
                }

                if (kategori === 'external') {
                    jenis.append('<option value="LPS">LPS</option>');
                    jenis.append('<option value="OJK">OJK</option>');
                }

                jenis.val(selected);
            }

            // load pertama
            loadJenisSurat();

            // ketika kategori berubah
            $('#kategori').change(function () {
                loadJenisSurat();
            });

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

            <label>Nama Peraturan</label><br> <input type="text" class="form-control" name="name"
                value="{{ $peraturan->name }}"> <br>

            <label>Kategori Peraturan</label> <select name="kategori" id="kategori" class="form-control" required>

                <option value="internal" {{ $peraturan->kategori == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="external" {{ $peraturan->kategori == 'external' ? 'selected' : '' }}>External</option>
            </select>
            <br>

            <label>Jenis Surat</label> <select name="jenis_surat" id="jenis_surat" class="form-control" required> </select>
            <br>

            <label>Nomor Surat Keputusan</label><br> <input type="text" class="form-control" name="nosk"
                value="{{ $peraturan->nosk }}"> <br>

            <label>Tanggal Surat Keputusan</label> <input type="date" class="form-control" name="tglsk"
                value="{{ $peraturan->tglsk }}"> <br>

            <label>Tanggal Berlaku Surat Keputusan</label> <input type="date" class="form-control" name="tgllaku"
                value="{{ $peraturan->tgllaku }}"> <br>

            <label>Uraian</label>

            <textarea class="form-control" name="uraian"
                placeholder="Uraian Isi Peraturan">{{ $peraturan->uraian }}</textarea>

            <br>

            <label>Upload PDF</label> <input type="file" accept="application/pdf" name="pdf" id="pdf"> <br>

            <label>Lampiran Peraturan</label>

            {{-- <textarea class="form-control" id="textarea" name="description"
                style="width: 100%; height: 500px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
    {{ $peraturan->description }}
    </textarea> --}}

            <br>
            <input type="submit" class="btn btn-primary" value="Save">

        </form>
    </div>
@endsection