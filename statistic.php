<?php
/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
require_once(__DIR__ . '/vendor/autoload.php');

$pathToDb = __DIR__ . '/store/' . $_GET['source'] . '.sqlite3';
if (!isset($_GET['source']) OR !file_exists(__DIR__ . '/store/' . $_GET['source'] . '.sqlite3')) {
    http_response_code(400);
    exit;
}

$Statistic = new \DenisBeliaev\logAnalyzer\Statistic($pathToDb);

\DenisBeliaev\logAnalyzer\U::jsonOut($Statistic->getHourlyData(), JSON_NUMERIC_CHECK);