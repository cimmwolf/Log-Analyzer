<?php
/**
 * @author: Denis Beliaev
 */

use DenisBeliaev\logAnalyzer\Log;

class LogTest extends PHPUnit_Framework_TestCase
{
    protected static $logName = 'temp';
    protected static $pdo;

    public static function setUpBeforeClass()
    {
        $dbPath = Log::getDbPath(self::$logName);
        copy(__DIR__ . '/../store/template.db', $dbPath);
        self::$pdo = new PDO('sqlite:' . $dbPath);

        $stmt = self::$pdo->prepare("INSERT INTO data (logdate, level, message) VALUES (:logdate, :level, :message)");
        for ($i = 0; $i <= 23; $i++) {
            $logDate = date('c', strtotime($i . ' hours ago'));
            $stmt->execute([':logdate' => $logDate, ':level' => 'error', ':message' => 'Test error']);
            $stmt->execute([':logdate' => $logDate, ':level' => 'info', ':message' => 'Test info']);
            $stmt->execute([':logdate' => $logDate, ':level' => 'warn', ':message' => 'Test warning']);
        }

        self::$pdo->query("INSERT INTO source (path, updated, timezone, last_request, last_size) VALUES ('/path/to/log.file',0, 'Europe/Moscow',0,0)");
        self::$pdo->query("INSERT INTO source (path, updated, timezone, last_request, last_size) VALUES ('/path/to/log2.file',0, 'Europe/Moscow',0,0)");
    }

    public static function tearDownAfterClass()
    {
        self::$pdo = null;
        unlink(Log::getDbPath(self::$logName));
    }

    public function testSources()
    {
        $expected = [
            ['/path/to/log.file', 0, 'Europe/Moscow', 0, 0],
            ['/path/to/log2.file', 0, 'Europe/Moscow', 0, 0],
        ];
        $Log = new Log(self::$logName);

        $this->assertEquals($expected, $Log->sources);
    }

    public function testSourceRemove()
    {
        $expected = [['/path/to/log2.file', 0, 'Europe/Moscow', 0, 0],];
        $Log = new Log(self::$logName);

        $Log->removeSource('/path/to/log.file');

        $this->assertEquals($expected, $Log->sources);
    }

    public function testSourceAdd()
    {
        $expected = [['/path/to/log2.file', 0, 'Europe/Moscow', 0, 0], [__DIR__ . '/test.nginx.log', 0, 'UTC', 0, 0],];
        $Log = new Log(self::$logName);

        $Log->addSource(__DIR__ . '/test.nginx.log', 'UTC');

        $this->assertEquals($expected, $Log->sources);
    }
}
