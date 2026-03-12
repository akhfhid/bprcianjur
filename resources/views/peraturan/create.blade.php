@extends('layouts.global')

@section('title')
    Create Data Peraturan
@endsection

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #334155;
        }

        .modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        }

        .card-header-modern {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            letter-spacing: 0.3px;
        }

        .form-control {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            /* padding: 12px 16px; */ /* INI KALO DI AKTIF IN ADA ANIMASI NYA BAGUS TAU COBAIN DEH :3 (minus nya placeholder kelelep) */
            font-size: 0.95rem;
            color: #0f172a;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            outline: none;
        }

        select.form-control:invalid {
            color: #94a3b8;
        }

        select.form-control option {
            color: #0f172a;
        }

        select.form-control option[value=""][disabled] {
            display: none;
        }

        .custom-file-upload {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 32px 24px;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
            transition: all 0.2s;
            display: block;
        }

        .custom-file-upload:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .custom-file-upload i {
            font-size: 2.5rem;
            color: #94a3b8;
            margin-bottom: 12px;
            transition: all 0.2s;
        }

        .custom-file-upload.has-file {
            border-color: #10b981;
            background: #ecfdf5;
            border-style: solid;
        }

        .custom-file-upload.has-file i {
            color: #ef4444;
        }

        .custom-file-upload.has-file #uploadText {
            color: #047857;
        }

        .note-editor.note-frame {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
        }

        .note-toolbar {
            background: #f8fafc !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        /* Buttons */
        .btn-submit {
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            padding: 12px 28px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-light {
            background: #ffffff;
            border: 1px solid #cbd5e1 !important;
            color: #475569;
            border-radius: 10px;
            padding: 12px 28px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-light:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Alert */
        .alert-modern {
            border-radius: 12px;
            border-left: 5px solid #10b981;
            background: #ecfdf5;
            color: #065f46;
            padding: 16px 20px;
            font-size: 0.95rem;
            font-weight: 500;
            border-top: 1px solid #d1fae5;
            border-right: 1px solid #d1fae5;
            border-bottom: 1px solid #d1fae5;
        }
    </style>

    <div class="container-fluid py-4">

        @if(session('status'))
            <div class="alert alert-modern mb-4 d-flex align-items-center shadow-sm">
                <i class="fas fa-check-circle mr-3" style="font-size: 1.2rem;"></i>
                {{ session('status') }}
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="modern-card">
                    <div class="card-body p-4 p-md-5">

                        <div class="card-header-modern">
                            <h4 class="mb-1 font-weight-bold text-dark">Tambah Peraturan Baru</h4>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Lengkapi formulir di bawah ini untuk
                                menambahkan data peraturan ke dalam sistem.</p>
                        </div>

                        <form enctype="multipart/form-data" action="{{ route('peraturan.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Nama Peraturan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="Misal: Peraturan Bank Indonesia No. 12" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Kategori Peraturan <span
                                                    class="text-danger">*</span></label>
                                            <select name="kategori" id="kategori" class="form-control" required>
                                                <option value="" selected disabled>-- Pilih Kategori --</option>
                                                <option value="internal">Internal</option>
                                                <option value="external">External</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Jenis Peraturan <span
                                                    class="text-danger">*</span></label>
                                            <select name="jenis_surat" id="jenis_surat" class="form-control" required>
                                                <option value="" selected disabled>-- Pilih Jenis --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4" id="subJenisWrapper" style="display:none;">
                                        <label class="form-label">Jenis OJK <span class="text-danger">*</span></label>
                                        <select name="sub_jenis" id="sub_jenis" class="form-control">
                                            <option value="" selected disabled>-- Pilih Jenis OJK --</option>
                                            <option value="POJK">POJK</option>
                                            <option value="SEOJK">SEOJK</option>
                                            <option value="PADK">PADK</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Nomor Peraturan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nosk"
                                            placeholder="Contoh: 010/SK.DIR/BPR-CJR/VI/2017" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Tanggal Peraturan <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tglsk" required>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Tanggal Berlaku <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tgllaku" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-light">

                            <div class="mb-4">
                                <label class="form-label">Uraian / Deskripsi</label>
                                <textarea class="form-control" name="uraian" id="uraian"></textarea>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Upload Dokumen (PDF) <span class="text-danger">*</span></label>
                                <label for="pdf" class="custom-file-upload" id="fileUploadLabel">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-cloud-upload-alt" id="uploadIcon"></i>
                                        <span class="mt-2 font-weight-bold text-dark" id="uploadText"
                                            style="font-size: 1.05rem;">
                                            Klik atau seret file PDF ke sini
                                        </span>
                                        <small class="text-muted mt-2" id="uploadInfo">
                                            Maksimal ukuran file 50MB
                                        </small>
                                    </div>
                                    <input type="file" accept="application/pdf" name="pdf" id="pdf" class="d-none" required>
                                </label>
                            </div>

                            <div class="d-flex justify-content-end align-items-center border-top pt-4">
                                <a href="{{ route('peraturan.index') }}" class="btn btn-light mr-3">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save mr-2"></i> Simpan Peraturan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer-scripts')

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function () {

            // Inisialisasi Summernote
            $('#uraian').summernote({
                height: 220,
                placeholder: 'Ketikkan uraian atau ringkasan peraturan di sini...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // Logika Kategori & Jenis Surat
            $('#kategori').change(function () {
                let kategori = $(this).val();
                let jenis = $('#jenis_surat');

                jenis.empty();
                jenis.append('<option value="" selected disabled>-- Pilih Jenis --</option>');

                $('#subJenisWrapper').slideUp();
                $('#sub_jenis').val('').removeAttr('required');

                if (kategori === 'internal') {
                    jenis.append('<option value="SK">Surat Keputusan (SK)</option>');
                    jenis.append('<option value="SE">Surat Edaran (SE)</option>');
                } else if (kategori === 'external') {
                    jenis.append('<option value="OJK">OJK</option>');
                    jenis.append('<option value="LPS">LPS</option>');
                }
            });

            $('#jenis_surat').change(function () {
                let jenis = $(this).val();
                if (jenis === 'OJK') {
                    $('#subJenisWrapper').slideDown();
                    $('#sub_jenis').attr('required', true); 
                } else {
                    $('#subJenisWrapper').slideUp();
                    $('#sub_jenis').val('').removeAttr('required');
                }
            });

            $('#pdf').on('change', function () {
                let file = this.files[0];
                let label = $('#fileUploadLabel');
                let icon = $('#uploadIcon');
                let text = $('#uploadText');

                if (file) {
                    label.addClass('has-file');
                    icon.removeClass('fa-cloud-upload-alt').addClass('fa-file-pdf');
                    text.text(file.name);
                } else {
                    label.removeClass('has-file');
                    icon.removeClass('fa-file-pdf').addClass('fa-cloud-upload-alt');
                    text.text('Klik atau seret file PDF ke sini');
                }
            });

        });
    </script>
@endsection