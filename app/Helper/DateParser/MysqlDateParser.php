<?php


namespace App\Helper\DateParser;

class MysqlDateParser extends DateParser
{
    private const DATE_FORMAT = 'Y-m-d';

    protected function getDateFormat(): string
    {
        return self::DATE_FORMAT;
    }
}