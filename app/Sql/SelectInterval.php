<?php

namespace App\Sql;

use App\Database;
use App\Entity\Interval;
use App\IntervalBuilder;
use RuntimeException;

class SelectInterval extends StaticSqlQuery
{
    /** @var IntervalBuilder */
    private $intervalBuilder;

    public function __construct(Database $database, IntervalBuilder $intervalBuilder)
    {
        parent::__construct($database);
        $this->intervalBuilder = $intervalBuilder;
    }

    public function fetch(Interval $interval): Interval
    {
        $mysqli_stmt = $this->createStatement();

        $formatted_date_start = $this->formatDate($interval->dateStart);
        $formatted_date_end = $this->formatDate($interval->dateEnd);

        $mysqli_stmt->bind_param(
            'isss',
            $interval->id,
            $formatted_date_start,
            $formatted_date_end,
            $interval->price
        );

        $mysqli_stmt->execute();
        if ($mysqli_stmt->affected_rows !== 1) {
            throw new RuntimeException('Interval was modified or not found for deletion.');
        }

        $mysqli_stmt_result = $mysqli_stmt->get_result();
        $mysqli_interval = $mysqli_stmt_result->fetch_assoc();
        $interval = $this->intervalBuilder->createFromMysqlResult($mysqli_interval);

        return $interval;
    }

    protected function createQuery(): string
    {
        $query = '
            SELECT *
                FROM price_by_interval 
                WHERE
                    id = ?
                    AND date_start = ?
                    AND date_end = ?
                    AND ROUND(price, 6) = ?
            FOR UPDATE 
        ';

        return $query;
    }
}
