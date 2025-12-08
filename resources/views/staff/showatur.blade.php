@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section('content')
<script type="text/javascript" src="{{ asset('canvas/pdf.min.js') }}"></script>
<script type="text/javascript">
    // Atur worker PDF.js (pastikan file pdf.worker.min.js ada di folder yang sama)
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";

    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
</script>

<hr class="my-3">
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <a href="{{ route('staff.peraturan') }}" class="btn btn-primary btn-sm">Back</a>
    </div>
</div>

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <div class="rounded mx-auto d-block">
                    <small>{{ Auth::user()->name }} - {{ Auth::user()->email }}</small>
                    <br>
                    {{ $time }}
                </div>
            </div>
            <div class="row wrapper">
            	<div style="text-center">{!! $peraturan->pdf !!}</div> 
                <div id="pdf-container" style="width: 100%;"></div> <!-- Tempat untuk menampilkan PDF -->

                <script>
                    const url = '{{ asset('storage/pdfs/' . $peraturan->pdf) }}'; // Path ke PDF
                    const container = document.getElementById('pdf-container'); // Tempat untuk menampilkan halaman-halaman PDF

                    // Fungsi untuk render halaman secara berurutan
                    async function renderPages(pdf) {
                        const numPages = pdf.numPages;

                        for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                            const page = await pdf.getPage(pageNum);
                            const viewport = page.getViewport({ scale: 1.5 });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            await page.render({
                                canvasContext: context,
                                viewport: viewport
                            }).promise;

                            container.appendChild(canvas); // Tambah canvas ke container
                        }
                    }

                    // Load dan render PDF
                    pdfjsLib.getDocument(url).promise.then(renderPages).catch(function(error) {
                        console.error("Error loading PDF: ", error);
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection