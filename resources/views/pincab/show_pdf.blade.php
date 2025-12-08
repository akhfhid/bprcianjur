@extends('layouts.global')
@section('title')Detail Peraturan @endsection
@section('content')
<script type="text/javascript" src="{{ asset('canvas/pdf.min.js') }}"></script>
<script type="text/javascript">
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";
    document.addEventListener('contextmenu', e => e.preventDefault());
</script>

<hr class="my-3">
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <a href="{{ route('pincab.peraturan') }}" class="btn btn-primary btn-sm">Back</a>
    </div>
</div>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col text-center">
                    <small>{{ Auth::user()->name }} - {{ Auth::user()->email }}</small><br>
                    {{ $time }}
                </div>
                <div class="col-auto">
                    <button id="btnPrintCanvas" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-print"></i> Print PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div id="pdf-container" style="width: 100%;"></div>
        </div>
    </div>
</div>

<script>
    const printedBy = "{{ Auth::user()->name }}";
    const copyright = "© COPYRIGHT BPR CIANJUR JABAR";
    const printDate = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });

    const container = document.getElementById('pdf-container');

    const pdfData = `{!! $peraturan->pdf !!}`;
    let imageList = [];
    let isPdfFile = false;

    if (pdfData.includes("<img")) {
        const temp = document.createElement("div");
        temp.innerHTML = pdfData;

        const imgs = temp.querySelectorAll("img");
        imgs.forEach(img => {
            let src = img.getAttribute("src");
            // if (!src.startsWith("http")) {
            //     src = window.location.origin + src;
            // }
            imageList.push(src);
        });
    } else if (pdfData.toLowerCase().endsWith(".pdf")) {
        isPdfFile = true;
    }

    if (isPdfFile) {
        const url = `/storage/pdfs/${pdfData}`;
        pdfjsLib.getDocument(url).promise.then(async (pdf) => {
            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const viewport = page.getViewport({ scale: 1.5 });
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                await page.render({ canvasContext: ctx, viewport }).promise;
                container.appendChild(canvas);
            }
        });
    } else if (imageList.length > 0) {
        // Render HTML gambar ke canvas
        imageList.forEach(src => {
            const img = new Image();
            img.crossOrigin = "anonymous";
            img.src = src;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0);
                container.appendChild(canvas);
            };
        });
    } else {
        container.innerHTML = `<p class="text-danger">File tidak dikenali atau tidak ditemukan.</p>`;
    }

    // --- PRINT BUTTON ---
    document.getElementById('btnPrintCanvas').addEventListener('click', () => {
        const canvases = container.querySelectorAll('canvas');
        if (!canvases.length) {
            alert("Tidak ada konten untuk dicetak.");
            return;
        }

        const w = window.open('', '_blank');
        w.document.open();
        w.document.write(`
            <html>
            <head>
                <title>Print Dokumen</title>
                <style>
                    @page { margin: 10mm; }
                    canvas { page-break-after: always; max-width: 100%; }
                    canvas:last-child { page-break-after: avoid; }
                </style>
            </head>
            <body></body>
            </html>
        `);
        w.document.close();

        canvases.forEach(c => {
            const clone = document.createElement('canvas');
            clone.width = c.width;
            clone.height = c.height;
            const ctx = clone.getContext('2d');
            ctx.drawImage(c, 0, 0);

            ctx.font = "bold 14px sans-serif";
            ctx.fillStyle = "rgba(0,0,0,0.5)";
            ctx.textAlign = "center";
            ctx.fillText(copyright, clone.width / 2, clone.height - 60);
            ctx.fillText("Printed By: " + printedBy, clone.width / 2, clone.height - 40);
            ctx.fillText("Print Date: " + printDate, clone.width / 2, clone.height - 20);

            w.document.body.appendChild(clone);
        });

        setTimeout(() => {
            w.print();
            w.close();
        }, 500);
    });
</script>


@endsection