<?php
$dates = ['2026-07-10','2026-07-11','2026-07-12','2026-07-13'];
foreach ($dates as $d) {
    $dt = new DateTime($d);
    echo $d . ': ' . $dt->format('l') . PHP_EOL;
}
