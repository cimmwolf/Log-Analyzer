<?php
require_once(__DIR__ . '/../vendor/autoload.php');

/*$time_start = microtime(true);
require_once __DIR__ . '/old_method.php';
$time_end = microtime(true);
$execution_time = $time_end - $time_start;
echo "Old method: Rows: $i, Total Execution Time: " . $execution_time . ' Sec';

echo PHP_EOL;*/

$time_start = microtime(true);
require_once __DIR__ . '/new_method.php';
$time_end = microtime(true);
$execution_time = $time_end - $time_start;
echo "New method: Rows: $i, Total Execution Time: " . $execution_time . ' Sec';