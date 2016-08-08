<?php
/**
 * Author: Denis Beliaev <cimmwolf@gmail.com>
 */

namespace DenisBeliaev\logAnalyzer;


class U
{
    public static function saveLog($pathToDb, $pathToLog, $timezone)
    {
        $pdo = new \PDO('sqlite:' . $pathToDb);

        $lastLogTime = $pdo->prepare('SELECT updated FROM source WHERE path = :pathToLog');
        $lastLogTime->execute([':pathToLog' => $pathToLog]);
        $lastLogTime = strtotime($lastLogTime->fetchColumn());

        $parser = new Parser(trim($pathToLog), $timezone);

        if ($parser->isUpdated($lastLogTime)) {
            $stmt = $pdo->prepare("INSERT INTO data (logdate, level, message) VALUES (:logdate, :level, :message)");
            foreach ($parser->parse() as $row) {
                if ($row['logTime'] > $lastLogTime) {
                    $stmt->execute([
                        ':logdate' => date('c', $row['logTime']),
                        ':level' => $row['level'],
                        ':message' => mb_convert_encoding($row['message'], 'utf-8')
                    ]);
                }
            }
            U::deleteOldRows($pathToDb);
        }

        $pathStmt = $pdo->prepare("INSERT INTO source (path) VALUES (:path)");
        $pathStmt->execute([':path' => $pathToLog]);

        $updStmt = $pdo->prepare("UPDATE source SET updated = :updated, timezone = :timezone WHERE path = :path");
        $updStmt->execute([':path' => $pathToLog, ':updated' => date('c', $parser->lastModified), ':timezone' => $timezone]);
    }

    private static function deleteOldRows($pathToDb)
    {
        $pdo = new \PDO('sqlite:' . $pathToDb);
        $pdo->query("DELETE FROM data WHERE datetime(logdate, 'localtime') < datetime('now', '-7 days', '-1 hours')");
    }
}