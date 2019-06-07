<?php


namespace App\Model;

use App\Entity\PriceList;

class IntervalsJoiner
{
    /** @var PriceListsUpdater */
    private $intervalUpdater;

    /** @var IntervalsOverrider */
    private $intervalOverrider;

    /** @var IntervalsInserter */
    private $intervalInserter;

    public function __construct(
        PriceListsUpdater $intervalUpdater,
        IntervalsOverrider $intervalOverrider,
        IntervalsInserter $intervalInserter
    ) {
        $this->intervalUpdater = $intervalUpdater;
        $this->intervalOverrider = $intervalOverrider;
        $this->intervalInserter = $intervalInserter;
    }

    public function joinIntervals(PriceList $saved_intervals, PriceList $newIntervals): void
    {
        $updated_intervals = $this->intervalUpdater->updateChangedIntervals($saved_intervals, $newIntervals);

        $unused_intervals = $saved_intervals->diffById($updated_intervals);
        $unsaved_intervals = $newIntervals->diff($updated_intervals);
        $unsaved_intervals = $this->intervalOverrider->overrideIntervals($unused_intervals, $unsaved_intervals);

        $this->intervalInserter->insertIntervals($unsaved_intervals);
    }
}