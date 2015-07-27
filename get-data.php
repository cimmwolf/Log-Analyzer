<?php
/**
 * Author: Denis Beliaev <cimmwolf@gmail.com>
 */
require_once(__DIR__ . '/vendor/autoload.php');
use DenisBeliaev\logAnalyzer\U;

$output = [];
foreach (glob(__DIR__ . "/store/*.sqlite3") as $pathToDb) {
    $pdo = new PDO('sqlite:' . $pathToDb);
    $logs = $pdo->query('SELECT path, timezone FROM source')->fetchAll(PDO::FETCH_ASSOC);

    $name = basename($pathToDb, '.sqlite3');
    try {
        foreach ($logs as $log)
            U::saveLog($pathToDb, $log['path'], $log['timezone']);
    } catch (Exception $e) {
        $name .= ' (ошибка обновления)';
    }

    $data = $pdo->query('SELECT *, COUNT(logdate) AS count FROM data GROUP BY message ORDER BY logdate DESC')->fetchAll(PDO::FETCH_ASSOC);

    $output[] = [
        'name' => $name,
        'data' => $data,
    ];
}

header('Content-Type: application/json');
echo json_encode($output);