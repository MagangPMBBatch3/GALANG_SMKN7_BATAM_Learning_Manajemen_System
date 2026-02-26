<?php
$lines = file('storage/logs/laravel.log');
$count = count($lines);
$start = max(0, $count - 20);
for ($i = $start; $i < $count; $i++) {
    echo $lines[$i];
}
