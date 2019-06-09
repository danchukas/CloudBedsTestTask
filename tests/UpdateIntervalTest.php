<?php


use App\Application;
use App\Database;
use PHPUnit\Framework\TestCase;

class UpdateIntervalTest extends TestCase
{
    /** @var Database */
    private $db;

    public function setUp(): void
    {
        $this->db = new Database(
            'cloudbeds_test_task_db',
            'tester',
            'tester',
            'cloudbeds_test_task'
        );

        $this->db->query('TRUNCATE price_by_interval');
    }

    public function intervalProvider(): array
    {
        $interval_for_update = [1, '2019-05-30', '2019-05-30', 11.10];

        return [
            'priceListWithSingleInterval' => [
                [
                    [1, '2019-05-29', '2019-05-31', 6]
                ],
                $interval_for_update
            ],
            'priceListWithManyIntervals' => [
                [
                    [1, '2019-05-29', '2019-05-31', 6],
                    [2, '2019-03-29', '2019-03-31', 8],
                ],
                $interval_for_update
            ],
        ];
    }

    /**
     * @dataProvider intervalProvider
     */
    public function testUpdateInterval(array $existed_intervals, array $new_interval)
    {
        if (!empty($existed_intervals)) {

            $stmt = $this->db->prepare('
                INSERT INTO price_by_interval
                    (id, date_start, date_end, price) 
                    VALUES (?, ?, ? , ?)
            ');

            foreach ($existed_intervals as $interval) {
                $stmt->bind_param('ssss', $interval[0], $interval[1], $interval[2], $interval[3]);
                $stmt->execute();
            }
        }

        $expected_saved_intervals = [];
        foreach ($existed_intervals as $interval) {
            if ($interval[0] === $new_interval[0]) {
                $interval_for_update = $interval;
                $interval = $new_interval;
            }
            $expected_saved_intervals[] = $interval;
        }

        $saved_intervals = $this->updateInterval($interval_for_update, $new_interval);

        $this->assertEquals($expected_saved_intervals, $saved_intervals);
    }

    protected function updateInterval(array $current_interval, array $new_interval): array
    {
        $_SERVER['REQUEST_URI'] = '/updateInterval';

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $current_interval_params = array_combine(['current_id', 'current_date_start', 'current_date_end', 'current_price'], $current_interval);
        $new_interval_params = array_combine(['new_id', 'new_date_start', 'new_date_end', 'new_price'], $new_interval);
        $_POST = $current_interval_params + $new_interval_params;

        $app = new Application();
        $app->run();

        $result = $this->db->query('
            SELECT 
                id, 
                date_start,
                date_end,
                price
            FROM price_by_interval
        ');

        $saved_data = $result->fetch_all(MYSQLI_NUM);

        return $saved_data;
    }
}
