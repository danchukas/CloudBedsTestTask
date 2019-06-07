<?php


namespace App\Helper\DateParser;

use DateTimeImmutable;

abstract class DateParser
{
    public function convertStringToDate($date): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat($this->getDateFormat(), $date);
        $beginOfDate = $date->setTime(0, 0, 0, 0);

        return $beginOfDate;
    }

    abstract protected function getDateFormat(): string;
}