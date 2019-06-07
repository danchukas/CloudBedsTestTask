<?php


namespace App\Model;


use App\Sql\CreatePriceByInterval;
use App\Entity\PriceList;

class IntervalsInserter
{
    /** @var CreatePriceByInterval */
    private $createPriceByInterval;

    public function __construct(CreatePriceByInterval $createPriceByInterval)
    {
        $this->createPriceByInterval = $createPriceByInterval;
    }

    public function insertIntervals(PriceList $intervals): void
    {
        foreach ($intervals as $interval) {
            $this->createPriceByInterval->run($interval);
        }
    }
}