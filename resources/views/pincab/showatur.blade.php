@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section ('content')

<script type="text/javascript" src="{{asset('canvas/pdf.min.js')}}"></script>

<script>
document.addEventListener('contextmenu', function(e){
    e.preventDefault();
});
</script>

<style>
.card-custom {
    border: none;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.header-info {
    font-size: 13px;
    opacity: 0.8;
}

.pdf-wrapper {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    overflow-x: auto;
}

#pdf-container canvas {
    margin-bottom: 20px;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}

.print-btn {
    border-radius: 30px;
    padding: 6px 18px;
    font-weight: 500;
}
</style>

<hr class="my-4">

<div class="row mb-4">
<div class="col-md-12 text-right">

@if(!$order)
    <a href="{{route('pincab.permohonandownload',$peraturan->id)}}" 
       class="btn btn-primary btn-sm ">
        Print
    </a>

@elseif($order->status != "SETUJU")
    <button class="btn btn-warning btn-sm print-btn" disabled>
        Menunggu Persetujuan Admin
    </button>

@elseif($order->print == "f")
    <a href="{{route('pincab.show_pdf',$peraturan->id)}}" 
       class="btn btn-success btn-sm ">
         Print 
    </a>

@else
    
    <a href="{{route('pincab.permohonandownload',$peraturan->id)}}" 
       class="btn btn-primary btn-sm shadow-sm">
    Print
    </a>
@endif

</div>
</div>

<div class="col-md-12">
    <div class="card card-custom">
        <div class="card-body">

            <div class="text-center mb-4">
                <div class="header-info">
                    <strong>{{Auth::user()->name}}</strong> | {{Auth::user()->email}} <br>
                    {{$time}}
                </div>
            </div>

            <!-- PDF Container -->
            <div class="pdf-wrapper">
                <div id="pdf-container"></div>
            </div>

            <!-- Footer Info -->
            <div class="text-center mt-4">
                <div class="header-info">
                    <strong>{{Auth::user()->name}}</strong> | {{Auth::user()->email}} <br>
                    {{$time}}
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const url = '{{ asset('storage/pdfs/' . $peraturan->pdf) }}';
const container = document.getElementById('pdf-container');

pdfjsLib.getDocument(url).promise.then(function(pdf) {
    const numPages = pdf.numPages;

    for (let pageNum = 1; pageNum <= numPages; pageNum++) {
        pdf.getPage(pageNum).then(function(page) {
            const viewport = page.getViewport({ scale: 1.4 });
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

@endsection