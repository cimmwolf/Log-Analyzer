<?php

namespace DenisBeliaev\logAnalyzer;

use DateTimeZone;
use DenisBeliaev\logAnalyzer\Exception\NoFileException;
use PDO;

/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 *
 * @property $sources array
 */
class Log
{
    public $dbPath;
    protected $pdo;

    /**
     * Constructor.
     * @param $name string Source database path
     * @throws NoFileException
     */
    public function __construct($name)
    {
        $this->dbPath = self::getDbPath($name);
        if (!file_exists($this->dbPath))
            throw new NoFileException("Database $name doesn't exists");

        $this->pdo = new PDO('sqlite:' . $this->dbPath);
    }

    public static function getDbPath($name)
    {
        return __DIR__ . '/../store/' . $name . '.sqlite3';
    }

    /**
     * @param $name
     * @return mixed
     * @throws \ErrorException
     */
    public function __get($name)
    {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }
        if (method_exists($this, 'set' . $name)) {
            throw new \ErrorException('Getting write-only property: ' . get_class($this) . '::' . $name);
        }
        throw new \ErrorException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    public function getSources()
    {
        return $this->pdo->query("SELECT * FROM source")->fetchAll(PDO::FETCH_NUM);
    }

    public function removeSource($path)
    {
        $statement = $this->pdo->prepare("DELETE FROM source WHERE path=:path");
        $statement->execute([':path' => $path]);
    }

    /**
     * @param $path
     * @param $timezone
     */
    public function addSource($path, $timezone)
    {
        if (!in_array($timezone, DateTimeZone::listIdentifiers()))
            throw new \UnexpectedValueException("Unknown timezone $timezone");

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            if (!U::isUrlExist($path))
                throw new \UnexpectedValueException("Not found URL $path");
        } elseif (!is_file($path))
            throw new \UnexpectedValueException("Not found path $path");

        $statement = $this->pdo->prepare("INSERT INTO source (path, timezone) VALUES (:path, :timezone)");
        $statement->execute([':path' => $path, ':timezone' => $timezone]);
    }
}