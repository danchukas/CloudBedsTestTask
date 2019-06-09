<?php


use App\Application;
use App\Database;
use PHPUnit\Framework\TestCase;

class AddIntervalTest extends TestCase
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
        $anyPrice = '2.00';
        $newInterval_1 = ['2019-05-30', '2019-05-30', '11.10'];
        $newInterval_2 = ['2019-03-29', '2019-03-31', '11.10'];

        return [
            'addFirstInterval' => [
                [],
                $newInterval_1,
                [
                    $newInterval_1
                ],
            ],
            'overwriteSameInterval' => [
                [
                    $newInterval_1
                ],
                $newInterval_1,
                [
                    $newInterval_1
                ],
            ],
            'overwriteBySamePrice' => [
                [
                    ['2019-03-10', '2019-03-30', '11.10']
                ],
                ['2019-03-29', '2019-03-31', '11.10'],
                [
                    ['2019-03-10', '2019-03-31', '11.10']
                ],
            ],
            'overwriteInnerIntervals' => [
                [
                    ['2019-03-29', '2019-03-29', $anyPrice],
                    ['2019-03-30', '2019-03-30', '11.10'],
                    ['2019-03-31', '2019-03-31', $anyPrice]
                ],
                $newInterval_2,
                [
                    $newInterval_2
                ],
            ],
            'divideInterval' => [
                [
                    ['2019-05-29', '2019-05-31', $anyPrice]
                ],
                $newInterval_1,
                [
                    ['2019-05-29', '2019-05-29', $anyPrice],
                    $newInterval_1,
                    ['2019-05-31', '2019-05-31', $anyPrice]
                ],
            ],
            'overwriteBeginInterval' => [
                [
                    ['2019-05-30', '2019-05-31', $anyPrice]
                ],
                $newInterval_1,
                [
                    ['2019-05-31', '2019-05-31', $anyPrice],
                    $newInterval_1
                ]
            ],
            'overwriteConsecutiveIntervals' => [
                [
                    ['2019-05-01', '2019-05-03', $anyPrice],
                    ['2019-05-04', '2019-05-21', '45'],
                    ['2019-05-22', '2019-05-25', $anyPrice]
                ],
                ['2019-05-03', '2019-05-21', $anyPrice],
                [
                    ['2019-05-01', '2019-05-25', $anyPrice]
                ]
            ]
        ];
    }

    /**
     * @dataProvider intervalProvider
     */
    public function testAddInterval(array $existed_intervals, array $newInterval, array $expected_saved_intervals)
    {
        if (!empty($existed_intervals)) {

            $stmt = $this->db->prepare('
                INSERT INTO price_by_interval
                    (date_start, date_end, price) 
                    VALUES (?, ? , ?)
            ');

            foreach ($existed_intervals as $interval) {
                $stmt->bind_param('sss', $interval[0], $interval[1], $interval[2]);
                $stmt->execute();
            }
        }

        $saved_intervals = $this->addInterval($newInterval);

        $this->assertEquals($expected_saved_intervals, $saved_intervals);
    }

    protected function addInterval(array $data): array
    {
        $_SERVER['REQUEST_URI'] = '/addInterval';

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array_combine(['date_start', 'date_end', 'price'], $data);

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
