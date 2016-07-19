<?php

namespace DenisBeliaev\logAnalyzer;

/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
class Statistic
{
    protected $pdo;

    /**
     * Statistic constructor.
     * @param $dbPath string Source database path
     */
    public function __construct($dbPath)
    {
        $this->pdo = new \PDO('sqlite:' . $dbPath);
    }

    /** Return statistic data for last 24 hours.
     * @return array Format: [[Label1, .., LabelN], [Value1, .., ValueN], ..]
     */
    public function getHourlyData()
    {
        $output = [['Info', 'Warnings', 'Errors']];
        $stmt = $this->pdo->prepare("
          SELECT level, COUNT(*) FROM data 
          WHERE strftime('%s', logdate) >= :start AND strftime('%s', logdate) <= :end
          GROUP BY level");

        for ($i = 0; $i <= 23; $i++) {
            $t = strtotime($i . ' hours ago');
            list($H, $n, $j, $Y) = explode(',', date('H,n,j,Y', $t));
            $stmt->execute([':start' => mktime($H, 0, 0, $n, $j, $Y), ':end' => mktime($H, 59, 59, $n, $j, $Y)]);

            list($info, $warnings, $errors) = [0, 0, 0];
            foreach ($stmt->fetchAll() as $row) {
                if ($row['level'] == 'info')
                    $info = $row[1];
                if ($row['level'] == 'warning')
                    $warnings = $row[1];
                if ($row['level'] == 'error')
                    $errors = $row[1];
            }
            $output[] = [$info, $warnings, $errors];
        }
        return $output;
    }
}