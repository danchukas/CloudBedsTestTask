<?php

namespace App\Sql;

use App\Entity\Interval;
use RuntimeException;

class UpdatePriceByInterval extends StaticSqlQuery
{
    public function run(Interval $interval): void
    {
        $mysqli_stmt = $this->createStatement();

        $formatted_date_start = $this->formatDate($interval->dateStart);
        $formatted_date_end = $this->formatDate($interval->dateEnd);

        $mysqli_stmt->bind_param(
            'ssdi',
            $formatted_date_start,
            $formatted_date_end,
            $interval->price,
            $interval->id
        );

        $mysqli_stmt->execute();

        if ($mysqli_stmt->affected_rows !== 1) {
            throw new RuntimeException('Interval was modified or not found for update.');
        }
    }

    protected function createQuery(): string
    {
        $query = "
            UPDATE price_by_interval 
                SET 
                    date_start=?
                    , date_end=?
                    , price=?
                WHERE
                    id = ?
        ";

        return $query;
    }
}
