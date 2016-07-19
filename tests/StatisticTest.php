<?php
use DenisBeliaev\logAnalyzer\Statistic;

/**
 * @author Denis Beliaev <cimmwolf@gmail.com>
 */
class StatisticTest extends \PHPUnit\Framework\TestCase
{
    protected $tempDb = __DIR__ . '/../store/_temp.sqlite3';
    protected $pdo;

    public function testHourly()
    {
        $Statistic = new Statistic($this->tempDb);
        $data = $Statistic->getHourlyData();
        $this->assertCount(25, $data);
        foreach ($data as $key => $row) {
            if ($key == 0)
                $this->assertTrue($row === ['Info', 'Warnings', 'Errors']);
            else
                $this->assertTrue($row == [1, 1, 1]);
        }
    }

    protected function setUp()
    {
        copy(__DIR__ . '/../store/template.db', $this->tempDb);
        $this->pdo = new PDO('sqlite:' . $this->tempDb);

        $stmt = $this->pdo->prepare("INSERT INTO data (logdate, level, message) VALUES (:logdate, :level, :message)");
        for ($i = 0; $i <= 23; $i++) {
            $logDate = date('c', strtotime($i . ' hours ago'));
            $stmt->execute([':logdate' => $logDate, ':level' => 'error', ':message' => 'Test error']);
            $stmt->execute([':logdate' => $logDate, ':level' => 'info', ':message' => 'Test info']);
            $stmt->execute([':logdate' => $logDate, ':level' => 'warning', ':message' => 'Test warning']);
        }
    }

    protected function tearDown()
    {
        $this->pdo = null;
        unlink($this->tempDb);
    }
}