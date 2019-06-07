<?php


namespace App\Model;

use App\Database;
use App\Entity\Interval;
use Throwable;

class PriceListEditor
{
    /** @var RelatedIntervalsSearcher */
    private $relatedIntervalsSearcher;

    /** @var IntervalsJoiner */
    private $intervalsJoiner;

    /** @var Database */
    private $database;

    public function __construct(
        RelatedIntervalsSearcher $relatedIntervalsSearcher,
        IntervalsJoiner $intervalsJoiner,
        Database $database
    ) {
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

            // todo: improve by multi_query
            // todo: improve by delete related intervals + insert correct intervals
            $this->intervalsJoiner->joinIntervals($saved_intervals, $new_price_list);

        } catch (Throwable $throwable) {
            $this->database->rollback();
            throw $throwable;
        }

        $this->database->commit();
    }
}