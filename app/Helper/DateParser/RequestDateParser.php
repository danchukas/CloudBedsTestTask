<?php


namespace App\Helper\DateParser;

class RequestDateParser extends DateParser
{
    private const DATE_FORMAT = 'Y-m-d';

    protected function getDateFormat(): string
    {
        return self::DATE_FORMAT;
    }
}