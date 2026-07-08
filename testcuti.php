<?php
// Simulate logic for date range July 10 to July 13
$awal = '2026-07-10';
$akhir = '2026-07-13';

$awlc = new DateTime($awal);
$akhirc = new DateTime($akhir);

$jmlcuti = 0;
$current = clone $awlc;
while ($current <= $akhirc) {
    $dow = (int)$current->format('N'); // 6=Sat, 7=Sun
    echo $current->format('Y-m-d l') . ' -> ' . ($dow >= 6 ? 'WEEKEND (skip)' : 'WEEKDAY (count)') . PHP_EOL;
    if ($dow < 6) {
        $jmlcuti++;
    }
    $current->modify('+1 day');
}

echo "Total hari kerja: " . $jmlcuti . PHP_EOL;
