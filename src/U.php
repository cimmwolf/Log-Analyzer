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

        $lastLogTime = $pdo->prepare('SELECT updated FROM source WHERE path = :pathToDb');
        $lastLogTime->execute([':pathToDb' => $pathToDb]);
        $lastLogTime = $lastLogTime->fetch()[0];

        $parser = new Parser(trim($pathToLog), $timezone);

        $pathStmt = $pdo->prepare("INSERT INTO source (path) VALUES (:path)");
        $updStmt = $pdo->prepare("UPDATE source SET updated = :updated, timezone = :timezone WHERE path = :path");
        $pathStmt->bindParam(':path', $pathToLog);
        $updStmt->bindParam(':path', $pathToLog);
        $updStmt->bindParam(':updated', date('c', $parser->lastModified));
        $updStmt->bindParam(':timezone', $timezone);

        if ($parser->isUpdated($lastLogTime)) {
            $stmt = $pdo->prepare("INSERT INTO data (logdate, level, message) VALUES (:logdate, :level, :message)");
            $stmt->bindParam(':logdate', $logDate);
            $stmt->bindParam(':level', $level);
            $stmt->bindParam(':message', $message);
            foreach ($parser->parse() as $row) {
                if ($row['logTime'] > strtotime('-3 days')) {
                    $logDate = date('c', $row['logTime']);
                    $level = $row['level'];
                    $message = mb_convert_encoding($row['message'], 'utf-8');
                    $stmt->execute();
                }
            }
            U::deleteOldRows($pathToDb);
        }
        $pathStmt->execute();
        $updStmt->execute();
    }

    private static function deleteOldRows($pathToDb)
    {
        $pdo = new \PDO('sqlite:' . $pathToDb);
        $pdo->query("DELETE FROM data WHERE datetime(logdate, 'localtime') < datetime('now', '-7 days', '-1 hours')");
    }
}