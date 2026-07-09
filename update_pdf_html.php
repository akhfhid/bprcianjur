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

    // Replace the pdf-container div if not already replaced
    $patternDiv = '/<div[^>]*id=["\']pdf-container["\'][^>]*><\/div>/i';
    if (preg_match($patternDiv, $c, $m)) {
        if (strpos($c, "html-container") === false) {
            $divMatch = $m[0];
            $newDiv = "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))\n" . 
                      "    $divMatch\n" . 
                      "@else\n" . 
                      "    <div id=\"html-container\" class=\"p-3\" style=\"width: 100%; overflow-x: auto; background: white;\">\n" .
                      "        {!! \$peraturan->pdf !!}\n" .
                      "    </div>\n" . 
                      "@endif";
            $c = str_replace($divMatch, $newDiv, $c);
            $updated = true;
        }
    }

    // Wrap the <script> tags for pdfjs in @if
    $patternScript = '/<script>\s*const url = \{!! json_encode\(asset[^\n]+;\s*const container = document\.getElementById.*?<\/script>/is';
    if (preg_match($patternScript, $c, $m)) {
        $scriptMatch = $m[0];
        if (strpos($c, "@if(preg_match('/\.pdf$/i', trim(\$peraturan->pdf)))") === false) {
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
