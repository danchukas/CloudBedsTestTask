<?php


namespace App\Model;


use App\Sql\UpdatePriceByInterval;
use App\Entity\PriceList;

class PriceListUpdater
{
    /** @var UpdatePriceByInterval */
    private $updatePriceByInterval;

    public function __construct(UpdatePriceByInterval $updatePriceByInterval)
    {
        $this->updatePriceByInterval = $updatePriceByInterval;
    }

    public function updateChangedIntervals(PriceList $saved_price_list, PriceList $new_price_list): PriceList
    {
        $changedIntervals = $new_price_list->diffWithSameExistedId($saved_price_list);

        foreach ($changedIntervals as $interval) {
            $this->updatePriceByInterval->run($interval);
        }

        return $changedIntervals;
    }
}