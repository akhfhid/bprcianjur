<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <title>Rules Pdf | {{ Auth::user()->name }}</title>
    <script type="text/javascript" src="{{ asset('canvas/pdf.min.js') }}"></script>
    <script type="text/javascript">
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
    <style>
        .watermark { position: relative; }
        .watermark::after {
            content: 'Printed By {{ Auth::user()->name }} - {{ Auth::user()->email }} -{{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}';
            position: absolute; bottom: 10px; top: 10px; right: 0; opacity: 0.5; font-size: 1.5em;
        }
    </style>
</head>

<body>
    <div class="container">
        <div>
            <div class="text-center">
                <small> Printed By {{ Auth::user()->name }} - {{ Auth::user()->email }} -
                    {{ $time = \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}</small>
                </div>
                                    <div class="watermark">
                <div id="pdf-container" style="width: 100%;"></div>
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
            </div>
            </div>
        </div>
        <br>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>
</body>

</html>
