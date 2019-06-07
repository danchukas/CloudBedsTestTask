<?php

namespace App\Sql;

use App\Entity\Interval;
use RuntimeException;

class DeletePriceByInterval extends StaticSqlQuery
{
    public function run(Interval $interval): void
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
    }

    protected function createQuery(): string
    {
        $query = '
            DELETE
                FROM price_by_interval 
                WHERE
                    id = ?
                    AND date_start = ?
                    AND date_end = ?
                    AND ROUND(price, 6) = ?
        ';

        return $query;
    }
}
