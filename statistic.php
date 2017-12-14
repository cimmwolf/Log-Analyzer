<?php
/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
require_once(__DIR__ . '/vendor/autoload.php');

if (!isset($_GET['source']) OR !file_exists(\DenisBeliaev\logAnalyzer\Log::getDbPath($_GET['source']))) {
    http_response_code(400);
    exit;
}

$Statistic = new \DenisBeliaev\logAnalyzer\Statistic($_GET['source']);

\DenisBeliaev\logAnalyzer\U::jsonOut($Statistic->getHourlyData(), JSON_NUMERIC_CHECK);