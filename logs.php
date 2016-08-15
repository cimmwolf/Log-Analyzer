<?php
require_once(__DIR__ . '/vendor/autoload.php');
use DenisBeliaev\logAnalyzer\U;

$output = [];
foreach (glob(__DIR__ . "/store/*.sqlite3") as $pathToDb)
    $output[] = basename($pathToDb, '.sqlite3');

U::jsonOut($output);