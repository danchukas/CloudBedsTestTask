<?php


namespace App\Model;


use App\Sql\DeletePriceByInterval;
use App\Sql\UpdatePriceByInterval;
use App\Entity\Interval;
use App\Entity\PriceList;

class IntervalsOverrider
{
    /** @var UpdatePriceByInterval */
    private $updatePriceByInterval;

    /** @var DeletePriceByInterval */
    private $deletePriceByInterval;

    public function __construct(UpdatePriceByInterval $updatePriceByInterval, DeletePriceByInterval $deletePriceByInterval)
    {
        $this->updatePriceByInterval = $updatePriceByInterval;
        $this->deletePriceByInterval = $deletePriceByInterval;
    }

    public function overrideIntervals(PriceList $saved_intervals, PriceList $newIntervals): PriceList
    {
        $unsaved_intervals = $newIntervals->cloneSelf();

        while ($saved_interval = $saved_intervals->removeFirstInterval()) {
            $unsaved_interval = $unsaved_intervals->removeFirstInterval();
            if ($unsaved_interval !== null) {
                $this->overrideInterval($saved_interval, $unsaved_interval);
            } else {
                // todo: delete all intervals by 1 query.
                $this->deleteInterval($saved_interval);
            }
        }

        return $unsaved_intervals;
    }

    private function overrideInterval(Interval $saved_interval, Interval $unsaved_interval): void
    {
        $unsaved_interval->id = $saved_interval->id;
        if ($unsaved_interval != $saved_interval) {
            $this->updatePriceByInterval->run($unsaved_interval);
        }
    }

    private function deleteInterval(?Interval $saved_interval): void
    {
        $this->deletePriceByInterval->run($saved_interval);
    }
}