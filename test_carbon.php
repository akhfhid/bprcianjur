<?php
require "vendor/autoload.php";
use Carbon\Carbon;
$awal = Carbon::parse("2026-07-03"); // Jumat
$akhir = Carbon::parse("2026-07-06"); // Senin

// Logic 1: diffInWeekdays
$jmlcuti1 = $awal->diffInWeekdays($akhir) + 1;

// Logic 2: diffInDaysFiltered
$jmlcuti2 = $awal->diffInDaysFiltered(function(Carbon $date) {
    return !$date->isWeekend();
}, $akhir->copy()->endOfDay()); // Use endOfDay to ensure the last day is counted if diff is time-based

echo "Awal: " . $awal->format("l, Y-m-d") . "\n";
echo "Akhir: " . $akhir->format("l, Y-m-d") . "\n";
echo "diffInWeekdays + 1: " . $jmlcuti1 . "\n";
echo "diffInDaysFiltered: " . $jmlcuti2 . "\n";

$awal = Carbon::parse("2026-07-06"); // Senin
$akhir = Carbon::parse("2026-07-06"); // Senin
echo "\nSenin to Senin\n";
echo "diffInWeekdays + 1: " . ($awal->diffInWeekdays($akhir) + 1) . "\n";
echo "diffInDaysFiltered: " . $awal->diffInDaysFiltered(function(Carbon $date) {
    return !$date->isWeekend();
}, $akhir->copy()->endOfDay()) . "\n";

