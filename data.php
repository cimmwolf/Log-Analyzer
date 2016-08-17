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

$pdo = \DenisBeliaev\logAnalyzer\U::getPdo($pathToDb);
$logs = $pdo->query('SELECT path, timezone, last_request FROM source')->fetchAll(PDO::FETCH_ASSOC);

foreach ($logs as $log)
    if ((time() - strtotime($log['last_request'])) > 60 * 2)
        \DenisBeliaev\logAnalyzer\U::saveLog($pathToDb, $log['path'], $log['timezone']);

$data = $pdo->query('SELECT strftime(\'%s\', logdate) AS logdate, level, message, COUNT(logdate) AS count FROM data GROUP BY message ORDER BY logdate DESC')->fetchAll(PDO::FETCH_ASSOC);
$output = $data;

\DenisBeliaev\logAnalyzer\U::jsonOut($output, JSON_NUMERIC_CHECK);