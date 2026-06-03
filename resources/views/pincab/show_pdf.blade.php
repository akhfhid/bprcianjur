@extends('layouts.global')

@section('title')
    Detail Peraturan
@endsection

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
                        <button id="btnPrintCanvas" class="btn btn-outline-primary btn-sm shadow-sm">
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

    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="printModalLabel"><i class="fas fa-print mr-2"></i> Persiapan Cetak</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                    <h6>Dokumen siap dicetak!</h6>
                    <p class="text-muted small mb-4">Dokumen ini akan dicetak dengan watermark keamanan otomatis.</p>

                    <div class="bg-light p-3 rounded text-left small">
                        <div class="row mb-1">
                            <div class="col-5 text-muted">Dicetak Oleh</div>
                            <div class="col-7 font-weight-bold">: {{ Auth::user()->name }}</div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-5 text-muted">Tanggal Cetak</div>
                            <div class="col-7 font-weight-bold">: <span id="modalDatePreview"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-5 text-muted">Watermark</div>
                            <div class="col-7 font-weight-bold text-success">: Aktif</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnKonfirmasiCetak">
                        <i class="fas fa-check mr-2"></i> Konfirmasi Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const printedBy = "{{ Auth::user()->name }}";
        const copyright = "© COPYRIGHT BPR CIANJUR JABAR";
        const now = new Date();

        const printDate =
            now.toLocaleDateString('id-ID') + ' ' +
            now.toTimeString().slice(0, 8);

        console.log(printDate);

        const container = document.getElementById('pdf-container');
        const pdfData = `{!! $peraturan->pdf !!}`;
        let imageList = [];
        let isPdfFile = false;


        function addDiagonalWatermark(canvas, ctx) {
            const patternCanvas = document.createElement('canvas');
            const pCtx = patternCanvas.getContext('2d');
            patternCanvas.width = 180;
            patternCanvas.height = 240;
            pCtx.translate(patternCanvas.width / 2, patternCanvas.height / 2);
            pCtx.rotate(-30 * Math.PI / 180);
            pCtx.textAlign = "center";
            pCtx.textBaseline = "middle";
            pCtx.fillStyle = "rgba(0, 102, 204, 0.4)"; //30%
            pCtx.font = "bold 16px sans-serif";
            pCtx.fillText(printedBy, 0, -10);
            pCtx.font = "bold 14px sans-serif";
            pCtx.fillText(printDate, 0, 12);
            ctx.save();
            const pattern = ctx.createPattern(patternCanvas, 'repeat');
            ctx.fillStyle = pattern;
            ctx.fillRect(0, 0, canvas.width, canvas.height); // Isi seluruh dokumen dengan pola
            ctx.restore();
        }

        if (pdfData.includes("<img")) {
            const temp = document.createElement("div");
            temp.innerHTML = pdfData;

            const imgs = temp.querySelectorAll("img");
            imgs.forEach(img => {
                let src = img.getAttribute("src");
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
                    const viewport = page.getViewport({
                        scale: 1.5
                    });
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    await page.render({
                        canvasContext: ctx,
                        viewport
                    }).promise;
                    addDiagonalWatermark(canvas, ctx);
                    container.appendChild(canvas);
                }
            });
        } else if (imageList.length > 0) {
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
                    addDiagonalWatermark(canvas, ctx);
                    container.appendChild(canvas);
                };
            });
        } else {
            container.innerHTML = `<p class="text-danger">File tidak dikenali atau tidak ditemukan.</p>`;
        }

        document.getElementById('btnPrintCanvas').addEventListener('click', () => {
            const canvases = container.querySelectorAll('canvas');
            if (!canvases.length) {
                alert("Dokumen masih memuat atau tidak ada konten untuk dicetak.");
                return;
            }

            document.getElementById('modalDatePreview').innerText = printDate;
            $('#printModal').modal('show');
        });

        document.getElementById('btnKonfirmasiCetak').addEventListener('click', () => {
            const btn = document.getElementById('btnKonfirmasiCetak');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            fetch('{{ route('pincab.update_print') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        order_id: {{ $order->id }} // Mengambil ID order dari controller
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 3. Jika berhasil di-update, barulah jalankan proses Print
                        $('#printModal').modal('hide');

                        const canvases = container.querySelectorAll('canvas');

                        const w = window.open('', '_blank');
                        w.document.title = "Print Dokumen - BPR Cianjur";
                        const style = w.document.createElement('style');
                        style.innerHTML = `
                    @page { margin: 10mm; }
                    body { margin: 0; padding: 0; background: #fff; text-align: center; }
                    canvas { page-break-after: always; max-width: 100%; display: block; margin: 0 auto; }
                    canvas:last-child { page-break-after: avoid; }
                `;
                        w.document.head.appendChild(style);

                        canvases.forEach(c => {
                            const clone = document.createElement('canvas');
                            clone.width = c.width;
                            clone.height = c.height;
                            const ctx = clone.getContext('2d');

                            ctx.drawImage(c, 0, 0);

                            ctx.font = "bold 14px sans-serif";
                            ctx.fillStyle = "rgba(0,0,0,0.5)";
                            ctx.textAlign = "center";

                            ctx.fillText(copyright, clone.width / 2, 40);
                            ctx.fillText("Printed By: " + printedBy + " - " + printDate, clone.width /
                                2, 60);

                            w.document.body.appendChild(clone);
                        });

                        setTimeout(() => {
                            w.print();
                            w.close();

                            // Setelah print selesai, otomatis redirect ke halaman status
                            window.location.href = "{{ route('pincab.peraturan') }}";
                        }, 500);

                    } else {
                        alert("Terjadi kesalahan saat memproses data pencetakan.");
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check mr-2"></i> Konfirmasi Cetak';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Gagal terhubung ke server.");
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i> Konfirmasi Cetak';
                });
        });
    </script>
@endsection
