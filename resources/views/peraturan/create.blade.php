@extends('layouts.global')
@section('title')
    Create Data Peraturan
@endsection

@section('footer-scripts')

    <script src="{{ asset('summernote/summernote-bs4.js') }}"></script>

    <script>
        $(function () {

            // summernote
            $('#textarea').summernote();

            // ketika kategori berubah
            $('#kategori').change(function () {

                let kategori = $(this).val();
                let jenis = $('#jenis_surat');

                jenis.empty(); // hapus option lama

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

        });
    </script>

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

            <label>Nama Peraturan</label><br> <input type="text" class="form-control" name="name"> <br>

            <label>Kategori Peraturan</label> <select name="kategori" id="kategori" class="form-control" required>

                <option value="">-- Pilih Kategori --</option>
                <option value="internal">Internal</option>
                <option value="external">External</option>
            </select>
            <br>

            <label>Jenis Surat</label> <select name="jenis_surat" id="jenis_surat" class="form-control" required>

                <option value="">-- Pilih Jenis --</option>
            </select>
            <br>
            <br>

            <label>Nomor Surat Keputusan</label><br> <input type="text" class="form-control" name="nosk"> <br>

            <label>Tanggal Surat Keputusan</label> <input type="date" class="form-control" name="tglsk"> <br>

            <label>Tanggal Berlaku Surat Keputusan</label> <input type="date" class="form-control" name="tgllaku"> <br>

            <label>Uraian</label>

            <textarea class="form-control" name="uraian" placeholder="Uraian Isi Peraturan"></textarea>

            <br>

            <label>Upload PDF</label> <input type="file" accept="application/pdf" name="pdf" id="pdf"> <br>

            <label>Lampiran Peraturan</label>

            {{-- <div class="summernote">
                <textarea class="form-control" id="textarea" name="description"
                    style="width: 100%; height: 500px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                    height="500px"></textarea>
            </div> --}}

            <br>
            <input type="submit" class="btn btn-primary" value="Save">

        </form>
    </div>
@endsection