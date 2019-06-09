<?php


namespace App\Model;

use App\Database;
use App\Entity\Interval;
use App\Sql\SelectInterval;
use Throwable;

class PriceListEditor
{
    /** @var SelectInterval */
    private $selectInterval;

    /** @var RelatedIntervalsSearcher */
    private $relatedIntervalsSearcher;

    /** @var IntervalsJoiner */
    private $intervalsJoiner;

    /** @var Database */
    private $database;

    public function __construct(
        SelectInterval $selectInterval,
        RelatedIntervalsSearcher $relatedIntervalsSearcher,
        IntervalsJoiner $intervalsJoiner,
        Database $database
    ) {
        $this->selectInterval = $selectInterval;
        $this->relatedIntervalsSearcher = $relatedIntervalsSearcher;
        $this->intervalsJoiner = $intervalsJoiner;
        $this->database = $database;
    }

    public function addInterval(Interval $newInterval): void
    {
        $this->database->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        try {
            $saved_intervals = $this->relatedIntervalsSearcher->searchRelatedIntervals($newInterval);
            $new_price_list = $saved_intervals->cloneSelf();

            $new_price_list->addInterval($newInterval);

            $this->intervalsJoiner->joinIntervals($saved_intervals, $new_price_list);

        } catch (Throwable $throwable) {
            $this->database->rollback();
            throw $throwable;
        }

        $this->database->commit();
    }

    public function updateInterval(Interval $currentInterval, Interval $newInterval): void
    {
        $this->database->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        try {
            $saved_intervals = $this->relatedIntervalsSearcher->searchRelatedIntervals($newInterval);
            // @todo: check existing of interval in DB, check existing of interval in PriceList
            $saved_intervals->addInterval($currentInterval);
            $new_price_list = $saved_intervals->cloneSelf();

            $new_price_list->deleteInterval($currentInterval);
            $new_price_list->addInterval($newInterval);

            $this->intervalsJoiner->joinIntervals($saved_intervals, $new_price_list);

        } catch (Throwable $throwable) {
            $this->database->rollback();
            throw $throwable;
        }

        $this->database->commit();
    }
}