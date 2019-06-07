<?php


namespace App\Entity;


use App\Helper\OneDay;
use DateTimeImmutable;

class Interval
{
    /** @var int|null */
    public $id;

    /** @var DateTimeImmutable */
    public $dateStart;

    /** @var DateTimeImmutable */
    public $dateEnd;

    /** @var float */
    public $price;

    public function __construct(
        DateTimeImmutable $dateStart,
        DateTimeImmutable $dateEnd,
        float $price,
        int $id = null
    ) {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->price = $price;
        $this->id = $id;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }

    public function copy(): self
    {
        $interval = $this->cloneSelf();
        $interval->id = null;

        return $interval;
    }

    public function moveStartAfter(self $interval): void
    {
        $this->dateStart = $interval->dateEnd->add(new OneDay());
    }

    public function moveEndBefore(self $interval): void
    {
        $this->dateEnd = $interval->dateStart->sub(new OneDay());
    }

    public function isConsecutiveAfter(self $interval): bool
    {
        $intervalsDifference = $this->dateStart->diff($interval->dateEnd);
        $daysBetweenIntervals = $intervalsDifference->days;
        $isConsecutive = $daysBetweenIntervals === 1;

        return $isConsecutive;
    }

    public function hasSamePriceWith(self $interval): bool
    {
        $hasSamePrice = $this->price === $interval->price;

        return $hasSamePrice;
    }

    public function isBefore(self $interval): bool
    {
        $isBefore = $this->dateEnd < $interval->dateStart;

        return $isBefore;
    }

    public function isItEndsNotLater($interval): bool
    {
        $endsLater = $this->dateEnd <= $interval->dateEnd;

        return $endsLater;
    }

    public function isItStartsNotLater($interval): bool
    {
        $startsLater = $this->dateStart <= $interval->dateStart;

        return $startsLater;
    }

    /**
     * Relate calls "clone $object" with method "__clone" of "$object"
     * because IDE (PHPSTORM) don't relate them.
     */
    public function cloneSelf(): self
    {
        $interval = clone $this;

        return $interval;
    }

    public function __clone()
    {
        $this->dateStart = clone $this->dateStart;
        $this->dateEnd = clone $this->dateEnd;
    }
}