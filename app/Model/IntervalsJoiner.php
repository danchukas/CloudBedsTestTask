<?php


namespace App\Model;

use App\Entity\PriceList;

class IntervalsJoiner
{
    /** @var PriceListUpdater */
    private $intervalUpdater;

    /** @var IntervalsOverrider */
    private $intervalOverrider;

    /** @var IntervalsInserter */
    private $intervalInserter;

    public function __construct(
        PriceListUpdater $intervalUpdater,
        IntervalsOverrider $intervalOverrider,
        IntervalsInserter $intervalInserter
    ) {
        $this->intervalUpdater = $intervalUpdater;
        $this->intervalOverrider = $intervalOverrider;
        $this->intervalInserter = $intervalInserter;
    }

    public function joinIntervals(PriceList $savedIntervals, PriceList $newIntervals): void
    {
        // todo: improve by multi_query
        // todo: improve by delete related intervals + insert correct intervals

        $updatedIntervals = $this->intervalUpdater->updateChangedIntervals($savedIntervals, $newIntervals);

        $unusedIntervals = $savedIntervals->diffById($updatedIntervals);
        $unsavedIntervals = $newIntervals->diff($updatedIntervals);
        $unsavedIntervals = $this->intervalOverrider->overrideIntervals($unusedIntervals, $unsavedIntervals);

        $this->intervalInserter->insertIntervals($unsavedIntervals);
    }
}