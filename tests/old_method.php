<?php
$time_start = microtime(true);

require_once(__DIR__ . '/../vendor/autoload.php');
use DenisBeliaev\logAnalyzer\Parser;


try {
    $parser = new Parser(__DIR__ . '/chopacho.log', 'Europe/Moscow');
} catch (\RuntimeException $e) {
    error_log($e->getMessage());
    return false;
}

$i = 0;

foreach ($parser->parse() as $row) {
    $i++;
}

try {
    $parser = new Parser(__DIR__ . '/test.yii.log', 'Europe/Moscow');
} catch (\RuntimeException $e) {
    error_log($e->getMessage());
    return false;
}

foreach ($parser->parse() as $row) {
    $i++;
}