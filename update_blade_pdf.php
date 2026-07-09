<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$pattern = "/const\s+url\s*=\s*'\{\{\s*asset\('storage\/pdfs\/'\s*\.\s*\\\$peraturan->pdf\)\s*\}\}';/";
$replacement = "const url = {!! json_encode(asset('storage/pdfs/' . trim(\$peraturan->pdf))) !!};";

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, $replacement, $content);
        file_put_contents($path, $newContent);
        echo "Updated $path\n";
    }
}

echo "Done\n";
