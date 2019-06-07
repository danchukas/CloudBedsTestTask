<?php


use App\Application;
use App\Database;
use PHPUnit\Framework\TestCase;

class GetFullPriceListTest extends TestCase
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
        return [
            'emptyPriceList' => [
                []
            ],
            'priceListWithSingleInterval' => [
                [
                    ['2019-03-10', '2019-03-30', '11.10']
                ]
            ],
            'priceListWithManyIntervals' => [
                [
                    ['2019-03-10', '2019-03-30', '11.10'],
                    ['2019-06-10', '2019-06-30', '11.10']
                ]
            ],
        ];
    }

    /**
     * @dataProvider intervalProvider
     */
    public function testGetFullPriceList(array $existed_intervals)
    {
        if (!empty($existed_intervals)) {

            $stmt = $this->db->prepare('
                INSERT INTO price_by_interval
                    (date_start, date_end, price) 
                    VALUES (?, ? , ?)
            ');

            foreach ($existed_intervals as &$interval) {
                $stmt->bind_param('sss', $interval[0], $interval[1], $interval[2]);
                $stmt->execute();
            }
        }

        $this->getFullPriceList();

        $result = $this->db->query('SELECT * FROM price_by_interval');
        $saved_intervals = $result->fetch_all(MYSQLI_NUM);

        $expected_data = [];
        foreach ($saved_intervals as $saved_interval) {
            $expected_interval = array_combine(['id', 'dateStart', 'dateEnd', 'price'], $saved_interval);
            $expected_data[] = $expected_interval;
        }

        $expected_formatted_data = json_encode($expected_data);

        $this->expectOutputString($expected_formatted_data);
    }

    protected function getFullPriceList(): void
    {
        $_SERVER['REQUEST_URI'] = '/getFullPriceList';

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $app = new Application();
        $app->run();
    }
}
