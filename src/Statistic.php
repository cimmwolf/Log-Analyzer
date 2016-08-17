<?php

namespace DenisBeliaev\logAnalyzer;

/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
class Statistic extends DbLog
{
    /** Return statistic data grouped by hours.
     * @return array Format: [[Label1, .., LabelN], [Value1, .., ValueN], ..]
     */
    public function getHourlyData()
    {
        $output = [['Date', 'Errors', 'Warnings', 'Info']];
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
                if ($row['level'] == 'warn')
                    $warnings = $row[1];
                if ($row['level'] == 'error' OR $row['level'] == 'crit')
                    $errors = $row[1];
            }
            if (count($output) > 1 OR ($info + $warnings + $errors) > 0) {
                $logTimestamp = $t + 60 * 60;
                if ($logTimestamp > time())
                    $logTimestamp = time();
                $output[] = [$logTimestamp, $errors, $warnings, $info];
            }

            $t += 60 * 60;
        }
        return $output;
    }
}