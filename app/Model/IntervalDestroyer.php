<?php


namespace App\Model;


use App\Sql\DeletePriceByInterval;
use App\Entity\Interval;

class IntervalDestroyer
{
    /** @var DeletePriceByInterval */
    private $deletePriceByInterval;

    public function __construct(DeletePriceByInterval $deletePriceByInterval)
    {
        $this->deletePriceByInterval = $deletePriceByInterval;
    }

    public function deleteInterval(Interval $interval): void
    {
        $this->deletePriceByInterval->run($interval);
    }
}