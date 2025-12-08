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
    // Nama user dari server (untuk watermark)
    const printedBy = "{{ Auth::user()->name }}";
    const copyright = "© COPYRIGHT BPR CIANJUR JABAR";
    const printDate = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });

    /* ---------- PDF.js ---------- */
    const url = '{{ asset('storage/pdfs/' . $peraturan->pdf) }}';
    const container = document.getElementById('pdf-container');

    async function renderPages(pdf) {
        const numPages = pdf.numPages;
        for (let pageNum = 1; pageNum <= numPages; pageNum++) {
            const page     = await pdf.getPage(pageNum);
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas   = document.createElement('canvas');
            const ctx      = canvas.getContext('2d');
            canvas.height  = viewport.height;
            canvas.width   = viewport.width;
            await page.render({ canvasContext: ctx, viewport }).promise;
            container.appendChild(canvas);
        }
    }

    pdfjsLib.getDocument(url).promise.then(renderPages).catch(err => console.error(err));

    /* ---------- Print only canvas + Watermark ---------- */
    document.getElementById('btnPrintCanvas').addEventListener('click', () => {
        const w = window.open('', '_blank');
        w.document.open();
        w.document.write(`
            <html>
              <head>
                <title>Print PDF</title>
                <style>
                  @page {
                    margin: 10mm;
                  }
                  @media print {
                    body { margin: 2; }
                    canvas {
                      display: block;
                      page-break-after: always;
                      page-break-inside: avoid;
                      max-width: 100%;
                      height: auto;
                    }
                    canvas:last-child {
                      page-break-after: avoid;
                    }
                  }
                </style>
              </head>
              <body></body>
            </html>
        `);
        w.document.close();
        const canvases = container.querySelectorAll('canvas');
        canvases.forEach(c => {
            const clone = document.createElement('canvas');
            clone.width  = c.width;
            clone.height = c.height;
            const ctx = clone.getContext('2d');
            ctx.drawImage(c, 0, 0);
            ctx.font = "bold 14px sans-serif";
            ctx.fillStyle = "rgba(0, 0, 0,)";
            ctx.textAlign = "center";
            ctx.fillText(copyright, clone.width / 2, clone.height - 1222);
            ctx.fillText("Printed By: " + printedBy, clone.width / 2, clone.height - 1206);
            ctx.fillText("Print Date: " + printDate, clone.width / 2, clone.height - 1191);
            w.document.body.appendChild(clone);
        });

        w.onload = () => { w.focus(); w.print(); w.close(); };
        setTimeout(() => { w.focus(); w.print(); w.close(); }, 500);
    });
</script>
@endsection