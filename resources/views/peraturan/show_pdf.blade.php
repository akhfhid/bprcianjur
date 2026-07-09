<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <title>Rules Pdf | {{ Auth::user()->name }}</title>
</head>

<body>
    <style>
        .watemark img{
            width: 100%;
        }
        .watermark {
            position: relative;
        }
        .watermark::after {
            content: 'Printed By {{ Auth::user()->name }} - {{ Auth::user()->email }} -{{ $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}';
            position: absolute;
            bottom: 10;
            top: 10;
            right: 0;
            opacity:0,5;
            font-size: 1,5em;
        }
    
#pdf-container canvas, #html-container img {
    max-width: 100% !important;
    height: auto !important;
}
</style>
    <div class="container">
        <div>
            <div class="watermark">
                @if(preg_match('/\.pdf$/i', trim($peraturan->pdf)))
    <div id="pdf-container" style="width: 100%;"></div>
@else
    <div id="html-container" class="p-3" style="width: 100%; overflow-x: auto; background: white;">
        {!! $peraturan->pdf !!}
    </div>
@endif
            </div>
        </div>
        <br>
    </div>

    <script src="{{ asset('canvas/pdf.min.js') }}"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";

        const url = "{{ route('peraturan.pdf_file', $peraturan->id) }}";
        const container = document.getElementById('pdf-container');

        async function renderPages(pdf) {
            const numPages = pdf.numPages;

            for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const viewport = page.getViewport({ scale: 1.3 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({
                    canvasContext: context,
                    viewport: viewport,
                }).promise;

                container.appendChild(canvas);
            }
        }

        pdfjsLib.getDocument(url).promise.then(renderPages).catch(function(error) {
            console.error('Error loading PDF:', error);
            container.innerHTML = '<p>Gagal membuka PDF. Silakan hubungi admin.</p>';
        });
    </script>
</body>

</html>
