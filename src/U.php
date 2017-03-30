<?php
/**
 * Author: Denis Beliaev <cimmwolf@gmail.com>
 */

namespace DenisBeliaev\logAnalyzer;


class U
{
    public static function saveLog($pathToDb, $pathToLog, $timezone)
    {
        date_default_timezone_set($timezone);

        $pdo = self::getPdo($pathToDb);

        $lastLogTime = $pdo->prepare('SELECT updated FROM source WHERE path = :pathToLog');
        $lastLogTime->execute([':pathToLog' => $pathToLog]);
        $lastLogTime = strtotime($lastLogTime->fetchColumn());
        if (!is_int($lastLogTime)) {
            if ((is_numeric($lastLogTime) AND !is_float($lastLogTime)) OR is_bool($lastLogTime) OR is_null($lastLogTime))
                $lastLogTime = intval($lastLogTime);
            else
                throw new \InvalidArgumentException('$timestamp parameter must be integer!');
        }

        $pdo->query('UPDATE source SET last_request = :now')->execute([':now' => date('c')]);

        try {
            $filename = trim($pathToLog);
            if (filter_var($filename, FILTER_VALIDATE_URL) !== FALSE) {
                $file_headers = @get_headers($filename, 1);
                if (strpos(array_values($file_headers)[0], '200 OK') === false)
                    throw new \RuntimeException("$filename does not exist!");
                if (isset($file_headers['Last-Modified']))
                    $mtime = $file_headers['Last-Modified'];
                elseif (isset($file_headers['X-Last-Modified']))
                    $mtime = $file_headers['X-Last-Modified'];
                if (isset($mtime))
                    $lastModified = strtotime($mtime);
            } else {
                if (!file_exists($filename))
                    throw new \RuntimeException("$filename does not exist!");
                $lastModified = filemtime($filename);
            }
        } catch (\RuntimeException $e) {
            error_log($e->getMessage());
            return false;
        }

        if (isset($lastModified) && $lastModified > $lastLogTime) {
            $stmt = $pdo->prepare("INSERT INTO data (logdate, level, message, raw) VALUES (:logdate, :level, :message, :raw)");

            $log = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (empty($log))
                return [];

            foreach ($log as $key => $string) {
                $parser = new Parser($string);

                $date = $parser->date;
                if ($date) {
                    if (strtotime($date) > $lastLogTime) {
                        $message = $parser->message;
                        if ($message) {
                            $message = $parser->filterMessage($message);
                            if (!empty($message)) {
                                $stmt->execute([
                                    ':logdate' => date('c', strtotime($date)),
                                    ':level' => $parser->messageType,
                                    ':message' => mb_convert_encoding($message, 'utf-8'),
                                    ':raw' => $parser->string
                                ]);
                            }
                        }
                    }
                }
            }

            $pdo->query("DELETE FROM data WHERE datetime(logdate, 'localtime') < datetime('now', '-7 days', '-1 hours')");

            $pathStmt = $pdo->prepare("INSERT INTO source (path) VALUES (:path)");
            $pathStmt->execute([':path' => $pathToLog]);

            $updStmt = $pdo->prepare("UPDATE source SET updated = :updated, timezone = :timezone WHERE path = :path");
            $updStmt->execute([':path' => $pathToLog, ':updated' => date('c', $lastModified), ':timezone' => $timezone]);
        }

        return true;
    }

    /**
     * @param $pathToDb
     * @return \PDO
     */
    public static function getPdo($pathToDb)
    {
        $pdo = new \PDO('sqlite:' . $pathToDb);
        new Migration($pdo);
        return $pdo;
    }

    public static function jsonOut($data, $options = 0)
    {
        header('Content-Type: application/json');
        $json = json_encode($data, $options);
        if ($json === false)
            $json = ['error' => json_last_error_msg()];
        echo $json;
    }
}