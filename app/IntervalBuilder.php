<?php


namespace App;

use App\Helper\DateParser\MysqlDateParser;
use App\Entity\Interval;

class IntervalBuilder
{
    /** @var MysqlDateParser */
    private $dateParser;

    public function __construct(MysqlDateParser $dateParser)
    {
        $this->dateParser = $dateParser;
    }

    public function createFromMysqlResult(array $interval): Interval
    {
        $date_start = $this->dateParser->convertStringToDate($interval['date_start']);
        $date_end = $this->dateParser->convertStringToDate($interval['date_end']);
        $price = $interval['price'];
        $id = $interval['id'];

        $interval = new Interval($date_start, $date_end, $price, $id);

        return $interval;
    }
}