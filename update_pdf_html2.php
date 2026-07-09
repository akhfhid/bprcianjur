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

    // Pattern to wrap script:
    // Finds `<script> \n const url = ... pdfjsLib ... </script>`
    $patternScript = '/<script>\s*const url = \{!! json_encode.*?pdfjsLib.*?<\/script>/is';
    if (preg_match($patternScript, $c, $m)) {
        $scriptMatch = $m[0];
        // Ensure not already wrapped exactly before this script match
        // We will just do a simple string replace
        if (strpos($c, "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . $scriptMatch) === false) {
            $newScript = "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . 
                         $scriptMatch . "\n" . 
                         "@endif";
            $c = str_replace($scriptMatch, $newScript, $c);
            $updated = true;
        }
    }
    
    // Also, handle the case where it might be `const url = '{{ asset...` (for those not replaced earlier)
    $patternScriptOld = '/<script>\s*const url = \'\{\{ asset.*?pdfjsLib.*?<\/script>/is';
    if (preg_match($patternScriptOld, $c, $m)) {
        $scriptMatch = $m[0];
        if (strpos($c, "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . $scriptMatch) === false) {
            $newScript = "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . 
                         $scriptMatch . "\n" . 
                         "@endif";
            $c = str_replace($scriptMatch, $newScript, $c);
            $updated = true;
        }
    }
    
    // Some show_pdf.blade.php might use `<script>\s*const url = "\{\{ route('peraturan.pdf_file'.*?pdfjsLib.*?<\/script>`
    $patternScriptRoute = '/<script>\s*const url = "\{\{ route.*?pdfjsLib.*?<\/script>/is';
    if (preg_match($patternScriptRoute, $c, $m)) {
        $scriptMatch = $m[0];
        if (strpos($c, "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . $scriptMatch) === false) {
            $newScript = "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . 
                         $scriptMatch . "\n" . 
                         "@endif";
            $c = str_replace($scriptMatch, $newScript, $c);
            $updated = true;
        }
    }
    
    // And finally `const url = `/storage/pdfs/${pdfData}`;`
    $patternScriptPdfData = '/<script>\s*let pdfData =.*?pdfjsLib.*?<\/script>/is';
    if (preg_match($patternScriptPdfData, $c, $m)) {
        $scriptMatch = $m[0];
        if (strpos($c, "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . $scriptMatch) === false) {
            $newScript = "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . 
                         $scriptMatch . "\n" . 
                         "@endif";
            $c = str_replace($scriptMatch, $newScript, $c);
            $updated = true;
        }
    }

    if ($updated) {
        file_put_contents($path, $c);
        echo "Updated $path\n";
    }
}

echo "Done\n";
