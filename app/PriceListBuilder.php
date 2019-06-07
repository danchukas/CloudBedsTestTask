<?php


namespace App;

use App\Entity\PriceList;

class PriceListBuilder
{
    /** @var IntervalBuilder */
    private $intervalBuilder;

    public function __construct(IntervalBuilder $intervalBuilder)
    {
        $this->intervalBuilder = $intervalBuilder;
    }

    public function createFromMysqlResult(array $mysql_intervals): PriceList
    {
        $intervals = [];

        foreach ($mysql_intervals as $mysql_interval) {
            $intervals[] = $this->intervalBuilder->createFromMysqlResult($mysql_interval);
        }

        $priceList = PriceList::createFromArray($intervals);

        return $priceList;
    }
}