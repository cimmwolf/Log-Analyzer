<?php
/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
require_once(__DIR__ . '/vendor/autoload.php');
use DenisBeliaev\logAnalyzer\U;

foreach (glob(__DIR__ . "/store/*.sqlite3") as $pathToDb) {
    $pdo = U::getPdo($pathToDb);
    $logs = $pdo->query('SELECT path, timezone, last_request FROM source')->fetchAll(PDO::FETCH_ASSOC);

    foreach ($logs as $log)
        if ((time() - strtotime($log['last_request'])) > 60 * 2)
            U::saveLog($pathToDb, $log['path'], $log['timezone']);
}