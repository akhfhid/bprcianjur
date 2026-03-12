@extends('layouts.global')

@section('title')
    Edit Data Peraturan
@endsection

@section('content')
    <!-- Font & Icons -->
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

        /* Fix Placeholder Select */
        select.form-control {
            color: #0f172a;
            /* Default hitam untuk edit karena sudah ada value */
        }

        select.form-control option {
            color: #0f172a;
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
            color: #10b981;
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

        .alert-modern {
            border-radius: 12px;
            border-left: 5px solid #10b981;
            background: #ecfdf5;
            color: #065f46;
            padding: 16px 20px;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Info file lama */
        .current-file-info {
            background: #f1f5f9;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 1rem;
        }

        .current-file-info i {
            margin-right: 8px;
            color: #ef4444;
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
                            <h4 class="mb-1 font-weight-bold text-dark">Edit Peraturan</h4>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Memperbarui data: <strong>{{ $peraturan->name }}</strong>
                            </p>
                        </div>

                        <form enctype="multipart/form-data" action="{{ route('peraturan.simpanedit', [$peraturan->id]) }}"
                            method="POST">
                            @csrf

                            <div class="row">
                                <!-- Col Left -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Nama Peraturan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name', $peraturan->name) }}" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Kategori Peraturan <span
                                                    class="text-danger">*</span></label>
                                            <select name="kategori" id="kategori" class="form-control" required>
                                                <option value="internal" {{ $peraturan->kategori == 'internal' ? 'selected' : '' }}>Internal</option>
                                                <option value="external" {{ $peraturan->kategori == 'external' ? 'selected' : '' }}>External</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Jenis Peraturan <span
                                                    class="text-danger">*</span></label>
                                            <select name="jenis_surat" id="jenis_surat" class="form-control" required>
                                                <!-- Options akan diisi via JS -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4" id="subJenisWrapper" style="display:none;">
                                        <label class="form-label">Jenis OJK <span class="text-danger">*</span></label>
                                        <select name="sub_jenis" id="sub_jenis" class="form-control">
                                            <option value="">-- Pilih Jenis OJK --</option>
                                            <option value="POJK" {{ $peraturan->jenis_ojk == 'POJK' ? 'selected' : '' }}>POJK
                                            </option>
                                            <option value="SEOJK" {{ $peraturan->jenis_ojk == 'SEOJK' ? 'selected' : '' }}>
                                                SEOJK</option>
                                            <option value="PADK" {{ $peraturan->jenis_ojk == 'PADK' ? 'selected' : '' }}>PADK
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Col Right -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label">Nomor Peraturan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nosk"
                                            value="{{ old('nosk', $peraturan->nosk) }}" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Tanggal Peraturan <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tglsk"
                                                value="{{ old('tglsk', $peraturan->tglsk) }}" required>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Tanggal Berlaku <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tgllaku"
                                                value="{{ old('tgllaku', $peraturan->tgllaku) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-light">

                            <div class="mb-4">
                                <label class="form-label">Uraian / Deskripsi</label>
                                <textarea class="form-control" name="uraian"
                                    id="uraian">{{ old('uraian', $peraturan->uraian) }}</textarea>
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Upload Dokumen (PDF)</label>

                                @if($peraturan->pdf)
                                    <div class="current-file-info mb-2">
                                        <i class="fas fa-file-pdf"></i> File saat ini:
                                        <a href="{{ asset('path_to_pdf/' . $peraturan->pdf) }}" target="_blank"
                                            class="font-weight-bold text-primary">{{ $peraturan->pdf }}</a>
                                        <small class="text-muted d-block mt-1">Upload file baru untuk mengganti file
                                            lama.</small>
                                    </div>
                                @endif

                                <label for="pdf" class="custom-file-upload" id="fileUploadLabel">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-cloud-upload-alt" id="uploadIcon"></i>
                                        <span class="mt-2 font-weight-bold text-dark" id="uploadText"
                                            style="font-size: 1.05rem;">
                                            Klik atau seret file PDF baru
                                        </span>
                                        <small class="text-muted mt-2" id="uploadInfo">
                                            Maksimal ukuran file 50MB
                                        </small>
                                    </div>
                                    <input type="file" accept="application/pdf" name="pdf" id="pdf" class="d-none">
                                </label>
                            </div>

                            <div class="d-flex justify-content-end align-items-center border-top pt-4">
                                <a href="{{ route('peraturan.index') }}" class="btn btn-light mr-3">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save mr-2"></i> Update Peraturan
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

            let oldKategori = "{{ $peraturan->kategori }}";
            let oldJenisSurat = "{{ $peraturan->jenis_surat }}";
            let oldSubJenis = "{{ $peraturan->jenis_ojk ?? '' }}";
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

            function loadJenisSurat(kategori, selectedValue) {
                let jenis = $('#jenis_surat');
                jenis.empty();

                if (kategori === 'internal') {
                    jenis.append('<option value="SK">Surat Keputusan (SK)</option>');
                    jenis.append('<option value="SE">Surat Edaran (SE)</option>');
                } else if (kategori === 'external') {
                    jenis.append('<option value="LPS">LPS</option>');
                    jenis.append('<option value="OJK">OJK</option>');
                }
                if (selectedValue) {
                    jenis.val(selectedValue);
                }
            }

            function toggleSubJenis(jenisValue, selectedSubJenis) {
                if (jenisValue === 'OJK') {
                    $('#subJenisWrapper').slideDown();
                    $('#sub_jenis').attr('required', true);
                    if (selectedSubJenis) {
                        $('#sub_jenis').val(selectedSubJenis);
                    }
                } else {
                    $('#subJenisWrapper').slideUp();
                    $('#sub_jenis').val('').removeAttr('required');
                }
            }

            loadJenisSurat(oldKategori, oldJenisSurat);
            toggleSubJenis(oldJenisSurat, oldSubJenis);
            $('#kategori').change(function () {
                let kategori = $(this).val();
                loadJenisSurat(kategori, null); 
                toggleSubJenis(null); 
            });

            $('#jenis_surat').change(function () {
                let jenis = $(this).val();
                toggleSubJenis(jenis);
            });

            // File Upload UI
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
                    text.text('Klik atau seret file PDF baru');
                }
            });

        });
    </script>
@endsection