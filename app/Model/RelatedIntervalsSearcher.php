<?php


namespace App\Model;


use App\Helper\OneDay;
use App\Sql\PriceListFinderByPeriod;
use App\Entity\Interval;
use App\Entity\PriceList;

class RelatedIntervalsSearcher
{
    /** @var PriceListFinderByPeriod */
    private $getIntervals;

    public function __construct(PriceListFinderByPeriod $createPriceByInterval)
    {
        $this->getIntervals = $createPriceByInterval;
    }

    public function searchRelatedIntervals(Interval $interval): PriceList
    {
        $oneDay = new OneDay();
        $dateFrom = $interval->dateStart->sub($oneDay);
        $dateTo = $interval->dateEnd->add($oneDay);
        $relatedIntervals = $this->getIntervals->findPriceListByPeriod($dateFrom, $dateTo);

        return $relatedIntervals;
    }
}