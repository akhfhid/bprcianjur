<?php

$dir = "app/Http/Controllers";
$files = [];

$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iter as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $changed = false;

    // For CutiController
    if (strpos($file, 'CutiController.php') !== false) {
        if (preg_match('/\$awal\s*=\s*Carbon::parse\(\$request->tglawal\);\s*\$akhir\s*=\s*Carbon::parse\(\$request->tglakhir\);/', $content)) {
            $loopCode = <<<EOF
\$awal = Carbon::parse(\$request->tglawal);
        \$akhir = Carbon::parse(\$request->tglakhir);
        
        \$jmlcuti = 0;
        \$current = \$awal->copy();
        while (\$current->lte(\$akhir)) {
            if (!\$current->isWeekend()) {
                \$jmlcuti++;
            }
            \$current->addDay();
        }
EOF;
            $content = preg_replace('/\$awal\s*=\s*Carbon::parse\(\$request->tglawal\);\s*\$akhir\s*=\s*Carbon::parse\(\$request->tglakhir\);/', $loopCode, $content);
            $content = preg_replace("/'jmlcuti'\s*=>\s*\\\$awal->diffInDays\(\\\$akhir\)\s*\+\s*1,/", "'jmlcuti' => \$jmlcuti,", $content);
            $changed = true;
        }
    } else {
        // For other controllers
        $pattern = '/(\$[a-zA-Z0-9_]+)\s*=\s*(\$[a-zA-Z0-9_]+)->diff[iI]nDays\((\$[a-zA-Z0-9_]+)\)(?:\s*\+\s*1)?;/';
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullMatch = $match[0];
                $varAssign = $match[1]; // e.g. $jmlcuti or $jumlahcuti
                $varStart = $match[2]; // e.g. $awlc
                $varEnd = $match[3]; // e.g. $akhirc
                
                $loopCode = <<<EOF
$varAssign = 0;
        \$current = {$varStart}->copy();
        while (\$current->lte({$varEnd})) {
            if (!\$current->isWeekend()) {
                {$varAssign}++;
            }
            \$current->addDay();
        }
EOF;
                $content = str_replace($fullMatch, $loopCode, $content);
                $changed = true;
            }
        }
    }

    if ($changed) {
        file_put_contents($file, $content);
        echo "Updated: $file\n";
    }
}
echo "Done.\n";
