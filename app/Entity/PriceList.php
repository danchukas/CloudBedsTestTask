<?php


namespace App\Entity;

use ArrayIterator;
use IteratorAggregate;
use LogicException;
use Traversable;

class PriceList implements IteratorAggregate
{
    /** @var Interval[] */
    private $intervals;

    public function __construct()
    {
        $this->intervals = [];
    }

    /**
     * @return Interval[]|ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->intervals);
    }

    public function removeFirstInterval(): ?Interval
    {
        return array_shift($this->intervals);
    }

    public static function createFromArray(array $arrayOfIntervals): self
    {
        self::validateArrayOfIntervals($arrayOfIntervals);

        $priceList = new self();
        $priceList->intervals = $arrayOfIntervals;

        return $priceList;
    }

    private static function validateArrayOfIntervals(array $arrayOfIntervals): void
    {
        foreach ($arrayOfIntervals as $interval) {
            if ($interval instanceof Interval) {
                continue;
            }

            throw new LogicException(self::class . ' operate only with ' . Interval::class);
        }
    }

    public function addInterval(Interval $interval): void
    {
        $this->removeCrossingDates($interval);
        $this->insertInterval($interval);
        $this->mergeConsecutiveIntervals();
    }

    private function removeCrossingDates(Interval $newInterval): void
    {
        foreach ($this->intervals as $position => $interval) {
            if ($newInterval->isItStartsNotLater($interval)) {
                $isIntervalFullyCrossedOver = $interval->isItEndsNotLater($newInterval);
                if ($isIntervalFullyCrossedOver) {
                    unset($this->intervals[$position]);
                } else {
                    $isIntervalBeginCrossedOver = $newInterval->dateEnd >= $interval->dateStart;
                    if ($isIntervalBeginCrossedOver) {
                        $interval->moveStartAfter($newInterval);
                    }
                    break;
                }
            } elseif ($newInterval->dateStart <= $interval->dateEnd) {
                $isIntervalCrossedInner = $newInterval->dateEnd < $interval->dateEnd;
                if ($isIntervalCrossedInner) {
                    $this->sliceIntervalLastPart($newInterval, $interval, $position);
                    $interval->moveEndBefore($newInterval);
                    break;
                } else {
                    $interval->moveEndBefore($newInterval);
                }
            }
        }
    }

    private function sliceIntervalLastPart(Interval $newInterval, Interval $interval, $position): void
    {
        $intervalLastPart = $interval->copy();
        $intervalLastPart->moveStartAfter($newInterval);
        $afterInterval = $position + 1;
        array_splice($this->intervals, $afterInterval, 0, [$intervalLastPart]);
    }

    private function insertInterval(Interval $newInterval): void
    {
        $interval_added = false;
        foreach ($this->intervals as $position => $interval) {
            if ($newInterval->isBefore($interval)) {
                array_splice($this->intervals, $position, 0, [$newInterval]);
                $interval_added = true;
                break;
            }
        }

        if (!$interval_added) {
            $this->intervals[] = $newInterval;
        }
    }

    private function mergeConsecutiveIntervals(): void
    {
        $prevKey = null;
        foreach ($this->intervals as $currentKey => $currentInterval) {
            if ($prevKey === null) {
                $prevKey = $currentKey;
                continue;
            }

            $prevInterval = $this->intervals[$prevKey];
            $intervals_have_same_price = $currentInterval->hasSamePriceWith($prevInterval);
            $intervals_are_consecutive = $currentInterval->isConsecutiveAfter($prevInterval);

            if ($intervals_have_same_price && $intervals_are_consecutive) {
                $this->mergeIntervals($currentKey, $prevKey);
            }

            $prevKey = $currentKey;
        }
    }

    private function mergeIntervals($current_key, $prev_key): void
    {
        $this->intervals[$current_key]->dateStart = clone $this->intervals[$prev_key]->dateStart;
        unset($this->intervals[$prev_key]);
    }

    public function diff(self $priceList): self
    {
        $diff = new self();

        foreach ($this->intervals as $interval) {
            foreach ($priceList->intervals as $comparedInterval) {
                if ($interval == $comparedInterval) {
                    continue 2;
                }
            }

            $diff->intervals[] = $interval;
        }

        return $diff;
    }

    public function diffById(self $priceList): self
    {
        $diff = new self();

        foreach ($this->intervals as $interval) {
            foreach ($priceList->intervals as $comparedInterval) {
                if ($interval->id === $comparedInterval->id) {
                    continue 2;
                }
            }

            $diff->intervals[] = $interval;
        }

        return $diff;
    }

    public function diffWithSameExistedId(self $priceList): self
    {
        $diffIntervals = new self;
        $intervals_with_id = $this->getWithId();

        foreach ($intervals_with_id as $interval) {
            foreach ($priceList->intervals as $compared_interval) {
                if ($interval->id === $compared_interval->id && $interval != $compared_interval) {
                    $diffIntervals->intervals[] = $interval;
                }
            }
        }

        return $diffIntervals;
    }

    private function getWithId(): self
    {
        $intervalsWithId = new self;

        foreach ($this->intervals as $interval) {
            if ($interval->hasId()) {
                $intervalsWithId->intervals[] = $interval;
            }
        }

        return $intervalsWithId;
    }

    public function deleteInterval(Interval $interval): void
    {
        foreach ($this->intervals as $key => $innerInterval) {
            if ($innerInterval == $interval) {
                unset($this->intervals[$key]);
                break;
            }
        }
    }

    /**
     * Relate calls "clone $object" with method "__clone" of "$object"
     * because IDE (PHPSTORM) don't relate them.
     */
    public function cloneSelf(): self
    {
        return clone $this;
    }

    public function __clone()
    {
        foreach ($this->intervals as &$interval) {
            $interval = $interval->cloneSelf();
        }
    }
}