@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section ('content')
<script type="text/javascript" src="{{asset('canvas/pdf.min.js')}}"></script>
<script type="text/javascript">
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";
	document.addEventListener('contextmenu',function(e){
		e.preventDefault();
	});
</script>

<hr class="my-3">
	<style>
#pdf-container canvas, #html-container img {
    max-width: 100% !important;
    height: auto !important;
}
</style>
<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('staff.peraturan')}}" class="btn btn-primary btn-sm">Back</a>
	</div>
</div>

<div class="col-md-12">
<div class="card">
<div class="card-body">

	<div class="text-center">
	<div class="rounded mx-auto d-block">
		<small>{{Auth::user()->name}} - {{Auth::user()->email}}</small>
		{{$time}}
	</div>
	</div>
	<style>
#pdf-container canvas, #html-container img {
    max-width: 100% !important;
    height: auto !important;
}
</style>
<div class="row wrapper">
	

                @if(preg_match('/\.pdf$/i', trim($peraturan->pdf)))
    <div id="pdf-container" style="width: 100%;"></div>
@else
    <div id="html-container" class="p-3" style="width: 100%; overflow-x: auto; background: white;">
        {!! $peraturan->pdf !!}
    </div>
@endif
                @if(preg_match('/\.pdf$/i', trim($peraturan->pdf)))
<script>
                    const url = {!! json_encode(asset('storage/pdfs/' . trim($peraturan->pdf))) !!}; 
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
                        container.innerHTML = '<p class="text-danger">Gagal membuka PDF. Silakan hubungi admin.</p>';
                    });
                </script>
@endif
</div>
	
</div>
</div>
</div>
</div>
@endsection