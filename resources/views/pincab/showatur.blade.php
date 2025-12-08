@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section ('content')
<script type="text/javascript" src="{{asset('canvas/pdf.min.js')}}">
</script>
<script type="text/javascript">
	document.addEventListener('contextmenu',function(e){
		e.preventDefault();
	});
</script> 
<hr class="my-3">
	<div class="row mb-3">
	<div class="col-md-12 text-right">
		<a href="{{route('pincab.permohonandownload',$peraturan->id)}}" class="btn btn-primary btn-sm">Print</a>
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
	<div class="row wrapper">
		{!! $peraturan->pdf !!}	
		<div id="pdf-container" style="width:1900px;"></div> <!-- Tempat untuk menampilkan PDF -->

					<script>
					    const url = '{{ asset('storage/pdfs/' . $peraturan->pdf) }}'; // Path ke PDF
					    console.log(url);
					    const container = document.getElementById('pdf-container'); // Tempat untuk menampilkan halaman-halaman PDF

					    pdfjsLib.getDocument(url).promise.then(function(pdf) {
					        const numPages = pdf.numPages; // Total halaman PDF

					        for (let pageNum = 1; pageNum <= numPages; pageNum++) {
					            pdf.getPage(pageNum).then(function(page) {
					                const viewport = page.getViewport({ scale: 1.5 });
					                const canvas = document.createElement('canvas');
					                const context = canvas.getContext('2d');
					                canvas.height = viewport.height;
					                canvas.width = viewport.width;

					                page.render({
					                    canvasContext: context,
					                    viewport: viewport
					                }).promise.then(function() {
					                    container.appendChild(canvas);
					                });
					            });
					        }
					    });
					</script>
				</div>
</div>
	<div class="text-center">
	<div class="rounded mx-auto d-block">
		<small>{{Auth::user()->name}} - {{Auth::user()->email}}</small>
		{{$time}}
	</div>
	</div>
</div>
</div>
</div>
</div>
@endsection