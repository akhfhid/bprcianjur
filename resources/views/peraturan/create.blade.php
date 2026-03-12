@extends('layouts.global')

@section('title')
    Create Data Peraturan
@endsection

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="col-md-12">
        <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{ route('peraturan.store') }}"
            method="POST">
            @csrf

            <label>Nama Peraturan</label>
            <input type="text" class="form-control" name="name">
            <br>

            <label>Kategori Peraturan</label>
            <select name="kategori" id="kategori" class="form-control" required>

                <option value="">-- Pilih Kategori --</option>
                <option value="internal">Internal</option>
                <option value="external">External</option>

            </select>
            <br>

            <label>Jenis Surat</label>
            <select name="jenis_surat" id="jenis_surat" class="form-control" required>

                <option value="">-- Pilih Jenis --</option>

            </select>
            <br>

            <div id="subJenisWrapper" style="display:none;">
                <label>Jenis OJK</label>

                <select name="sub_jenis" id="sub_jenis" class="form-control">

                    <option value="">-- Pilih Jenis OJK --</option>
                    <option value="POJK">POJK</option>
                    <option value="SE OJK">SE OJK</option>
                    <option value="PADK">PADK</option>

                </select>

                <br>
            </div>

            <label>Nomor Surat Keputusan</label>
            <input type="text" class="form-control" name="nosk">
            <br>

            <label>Tanggal Surat Keputusan</label>
            <input type="date" class="form-control" name="tglsk">
            <br>

            <label>Tanggal Berlaku Surat Keputusan</label>
            <input type="date" class="form-control" name="tgllaku">
            <br>

            <label>Uraian</label>

            <textarea class="form-control" name="uraian" placeholder="Uraian Isi Peraturan"></textarea>

            <br>

            <label>Upload PDF</label>
            <input type="file" accept="application/pdf" name="pdf" id="pdf">
            <br>

            <input type="submit" class="btn btn-primary" value="Save">

        </form>
    </div>

@endsection


@section('footer-scripts')

    <script src="{{ asset('summernote/summernote-bs4.js') }}"></script>

    <script>

        $(function () {

            $('#textarea').summernote();

            $('#kategori').change(function () {

                let kategori = $(this).val();
                let jenis = $('#jenis_surat');

                jenis.empty();

                // reset sub jenis
                $('#subJenisWrapper').hide();
                $('#sub_jenis').val('');

                if (kategori === 'internal') {

                    jenis.append('<option value="">-- Pilih Jenis --</option>');
                    jenis.append('<option value="SK">Surat Keputusan (SK)</option>');
                    jenis.append('<option value="SE">Surat Edaran (SE)</option>');

                }

                if (kategori === 'external') {

                    jenis.append('<option value="">-- Pilih Jenis --</option>');
                    jenis.append('<option value="OJK">OJK</option>');
                    jenis.append('<option value="LPS">LPS</option>');

                }

            });


            $('#jenis_surat').change(function () {

                let jenis = $(this).val();

                if (jenis === 'OJK') {
                    $('#subJenisWrapper').show();
                }
                else {
                    $('#subJenisWrapper').hide();
                    $('#sub_jenis').val('');
                }

            });

        });

    </script>

@endsection