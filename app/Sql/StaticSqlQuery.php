<?php


namespace App\Sql;


use App\Database;
use DateTimeInterface;
use mysqli_stmt;

abstract class StaticSqlQuery
{
    private const MYSQL_DATE_FORMAT = 'Y-m-d';

    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    protected function formatDate(DateTimeInterface $dateStart): string
    {
        return $dateStart->format(self::MYSQL_DATE_FORMAT);
    }

    protected function createStatement(): mysqli_stmt
    {
        $query = $this->createQuery();
        $mysqli_stmt = $this->database->prepare($query);

        return $mysqli_stmt;
    }

    abstract protected function createQuery(): string;
}