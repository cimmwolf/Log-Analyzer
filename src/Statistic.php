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

    /** Return statistic data grouped by hours.
     * @return array Format: [[Label1, .., LabelN], [Value1, .., ValueN], ..]
     */
    public function getHourlyData()
    {
        $output = [['Date', 'Info', 'Warnings', 'Errors']];
        $stmt = $this->pdo->prepare("
          SELECT level, COUNT(*) FROM data 
          WHERE strftime('%s', logdate) >= :start AND strftime('%s', logdate) <= :end
          GROUP BY level");

        $t = strtotime('1 week ago');
        list($H, $n, $j, $Y) = explode(',', date('H,n,j,Y', $t));
        $t = mktime($H, 0, 0, $n, $j, $Y);
        while ($t < time()) {
            $stmt->execute([':start' => $t, ':end' => $t + 60 * 60 - 1]);

            list($info, $warnings, $errors) = [0, 0, 0];
            foreach ($stmt->fetchAll() as $row) {
                if ($row['level'] == 'info')
                    $info = $row[1];
                if ($row['level'] == 'warning')
                    $warnings = $row[1];
                if ($row['level'] == 'error')
                    $errors = $row[1];
            }
            if (count($output) > 1 OR ($info + $warnings + $errors) > 0)
                $output[] = [date('H:00 j M', $t + 60 * 60), $info, $warnings, $errors];

            $t += 60 * 60;
        }
        return $output;
    }
}