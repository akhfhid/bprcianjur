<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isDir()) continue;
    $filename = $file->getFilename();
    if ($filename !== 'showatur.blade.php' && $filename !== 'show_pdf.blade.php') continue;
    
    $path = $file->getPathname();
    $c = file_get_contents($path);
    
    $updated = false;
    
    // Check if the style section exists
    if (strpos($c, '</style>') !== false) {
        // Find if we already added max-width
        if (strpos($c, 'max-width: 100% !important;') === false) {
            
            // If #pdf-container canvas is there, replace it
            if (strpos($c, '#pdf-container canvas {') !== false) {
                $c = str_replace(
                    '#pdf-container canvas {',
                    "#pdf-container canvas, #html-container img {\n    max-width: 100% !important;\n    height: auto !important;",
                    $c
                );
                $updated = true;
            } else {
                // Just add it before </style>
                $newCSS = "\n#pdf-container canvas, #html-container img {\n    max-width: 100% !important;\n    height: auto !important;\n}\n";
                $c = str_replace('</style>', $newCSS . '</style>', $c);
                $updated = true;
            }
        }
    } else {
        // If there's no <style> block, let's inject it before </head> or just before <hr class="my-4">
        if (strpos($c, 'max-width: 100% !important;') === false) {
            $newCSS = "<style>\n#pdf-container canvas, #html-container img {\n    max-width: 100% !important;\n    height: auto !important;\n}\n</style>\n";
            // Find a good place, e.g., before the first <hr class="my-4"> or <div class="row
            if (strpos($c, '<hr class="my-4">') !== false) {
                $c = str_replace('<hr class="my-4">', $newCSS . '<hr class="my-4">', $c);
                $updated = true;
            } elseif (strpos($c, '<div class="row') !== false) {
                $c = str_replace('<div class="row', $newCSS . '<div class="row', $c);
                $updated = true;
            }
        }
    }

    if ($updated) {
        file_put_contents($path, $c);
        echo "Updated CSS in $path\n";
    }
}

echo "Done\n";
