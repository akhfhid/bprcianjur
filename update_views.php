<?php

$dir = "resources/views";
$files = [];

$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iter as $file) {
    if ($file->isFile() && preg_match("/(showatur\.blade\.php|show_pdf\.blade\.php)$/", $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

$pdfJsContainer = <<<EOF
                <div id="pdf-container" style="width: 100%;"></div>
                <script>
                    const url = '{{ asset('storage/pdfs/' . \$peraturan->pdf) }}'; 
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
EOF;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $changed = false;
    
    if (strpos($file, "showatur.blade.php") !== false) {
        // ensure pdf.js is loaded
        if (strpos($content, "pdf.min.js") === false) {
            $scriptTags = <<<EOF
<script type="text/javascript" src="{{asset('canvas/pdf.min.js')}}"></script>
<script type="text/javascript">
    pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('canvas/pdf.worker.min.js') }}";
	document.addEventListener('contextmenu',function(e){
		e.preventDefault();
	});
</script>
EOF;
            // Inject after @section('content')
            $content = preg_replace("/@section\s*\(\s*'content'\s*\)/i", "$0\n" . $scriptTags, $content);
            $changed = true;
        }

        // Check if {!! $peraturan->pdf !!} exists
        if (preg_match('/<div[^>]*>\s*{!!\s*\$peraturan->pdf\s*!!}\s*<\/div>/i', $content)) {
            $content = preg_replace('/<div[^>]*>\s*{!!\s*\$peraturan->pdf\s*!!}\s*<\/div>/i', '', $content);
            $changed = true;
        } elseif (preg_match('/{!!\s*\$peraturan->pdf\s*!!}/', $content)) {
            $content = preg_replace('/{!!\s*\$peraturan->pdf\s*!!}/', '', $content);
            $changed = true;
        }

        if (strpos($content, 'id="pdf-container"') === false) {
            // Need to insert the pdfJsContainer in the row wrapper
            // Usually after class="row wrapper"
            if (preg_match('/<div class="row wrapper">\s*/i', $content)) {
                $content = preg_replace('/(<div class="row wrapper">\s*)/i', "$1\n" . $pdfJsContainer . "\n", $content);
                $changed = true;
            }
        }
    } else {
        // show_pdf.blade.php
        if (strpos($content, "pdf.min.js") === false) {
            $headInject = <<<EOF
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
EOF;
            $content = str_replace("</head>", $headInject, $content);
            
            $containerWrap = <<<EOF
            <div class="watermark">
$pdfJsContainer
            </div>
EOF;
            $content = preg_replace('/{!!\s*\$peraturan->pdf\s*!!}/', $containerWrap, $content);
            $changed = true;
        }
    }
    
    if ($changed) {
        file_put_contents($file, $content);
        echo "Updated: $file\n";
    }
}
echo "Done.\n";
