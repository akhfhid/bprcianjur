
@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section('content')
<script type="text/javascript" src="{{ asset('canvas/pdf.min.js') }}"></script>
<script type="text/javascript">
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";

    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
</script>
<hr class="my-3">
<div class="row mb-3">
    <div class="col-md-12 text-right">
		<a href="{{route('kadiv.permohonandownload', $peraturan->id)}}" class="btn btn-primary">Print</a>
        
        <a href="{{ route('kadiv.peraturan') }}" class="btn btn-primary btn-sm">Back</a>
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
                <div id="pdf-container" style="width: 1800px;"></div> <!-- Tempat untuk menampilkan PDF -->

                <script>
                    const url = '{{ asset('storage/pdfs/' . $peraturan->pdf) }}'; 
                    const container = document.getElementById('pdf-container'); 

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
                            container.appendChild(canvas); 
                        }
                    }

                    pdfjsLib.getDocument(url).promise.then(renderPages).catch(function(error) {
                        console.error("Error loading PDF: ", error);
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
@include('peraturan.partials.activity_timer', ['peraturan' => $peraturan])