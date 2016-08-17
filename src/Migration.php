<?php

namespace DenisBeliaev\logAnalyzer;

/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
class Migration
{
    protected $pdo;

    /**
     * Migration constructor.
     * @param $pdo \PDO
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $stmt = $pdo->query("SELECT COUNT(*) FROM data WHERE message LIKE '%client:%' OR message LIKE '% *%' OR message LIKE '%temporary file /%' OR level = 'warning'");
        if ($stmt->fetchColumn() > 0)
            $this->migrationV010("SELECT logdate, message, level FROM data WHERE message LIKE '%client:%' OR message LIKE '%*%' OR message LIKE '%temporary file /%' OR level = 'warning'");

        $stmt = $pdo->query('PRAGMA table_info(source)');
        if ($stmt->fetchAll()[3]['name'] != 'last_request')
            $this->migrationV020();
    }

    /**
     * @param $sql string
     */
    private function migrationV010($sql)
    {
        $stmt = $this->pdo->prepare("UPDATE data SET message = :after, level = :level WHERE message = :before AND logdate = :logdate");
        foreach ($this->pdo->query($sql)->fetchAll() as $row) {
            $message = preg_replace('~\*\d+ ~', '', $row[1]);
            $message = preg_replace('~(.*?) client:.*?,(.*)~', '$1$2', $message);
            $message = preg_replace('~(.*? to a temporary file) [a-zA-Z0-9_/]+( .*)~', '$1$2', $message);
            $stmt->execute([':logdate' => $row[0], ':before' => $row[1], ':after' => $message, ':level' => str_replace('warning', 'warn', $row[2])]);
        }
    }

    private function migrationV020()
    {
        $stmt = $this->pdo->query('ALTER TABLE source ADD COLUMN last_request INT');
        $stmt->execute();
    }
}