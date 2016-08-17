<?php

namespace DenisBeliaev\logAnalyzer;

/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
class DbLog
{
    protected $pdo;

    /**
     * Constructor.
     * @param $dbPath string Source database path
     */
    public function __construct($dbPath)
    {
        $this->pdo = new \PDO('sqlite:' . $dbPath);
        new Migration($this->pdo);
    }
}