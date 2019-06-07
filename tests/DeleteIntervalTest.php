<?php


use App\Application;
use App\Database;
use PHPUnit\Framework\TestCase;

class DeleteIntervalTest extends TestCase
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
        $interval_for_deletion = ['1', '2019-05-30', '2019-05-30', '11.10'];

        return [
            'priceListWithSingleInterval' => [
                [
                    $interval_for_deletion
                ],
                $interval_for_deletion
            ],
            'priceListWithManyIntervals' => [
                [
                    $interval_for_deletion,
                    ['2019-06-10', '2019-06-30', 11.1],
                ],
                $interval_for_deletion
            ],
        ];
    }

    /**
     * @dataProvider intervalProvider
     */
    public function testDeleteInterval(array $existed_intervals, array $interval_for_deletion)
    {
        if (!empty($existed_intervals)) {

            $stmt = $this->db->prepare('
                INSERT INTO price_by_interval
                    (id, date_start, date_end, price) 
                    VALUES (?, ?, ? , ?)
            ');

            foreach ($existed_intervals as &$interval) {
                $stmt->bind_param('ssss', $interval[0], $interval[1], $interval[2], $interval[3]);
                $stmt->execute();
            }
        }

        $saved_intervals = $this->deleteInterval($interval_for_deletion);

        $expected_saved_intervals = array_diff($saved_intervals, [$interval_for_deletion]);

        $this->assertEquals($expected_saved_intervals, $saved_intervals);
    }

    protected function deleteInterval(array $data): array
    {
        $_SERVER['REQUEST_URI'] = '/deleteInterval';

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array_combine(['id', 'date_start', 'date_end', 'price'], $data);

        $app = new Application();
        $app->run();

        $result = $this->db->query('
            SELECT 
                date_start,
                date_end,
                price
            FROM price_by_interval
        ');

        $saved_data = $result->fetch_all(MYSQLI_NUM);

        return $saved_data;
    }
}
