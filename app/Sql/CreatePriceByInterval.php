<?php

namespace App\Sql;

use App\Entity\Interval;

class CreatePriceByInterval extends StaticSqlQuery
{
    public function run(Interval $interval): void
    {
        $mysqli_stmt = $this->createStatement();

        $formatted_date_start = $this->formatDate($interval->dateStart);
        $formatted_date_end = $this->formatDate($interval->dateEnd);

        $mysqli_stmt->bind_param(
            'ssd',
            $formatted_date_start,
            $formatted_date_end,
            $interval->price
        );

        $mysqli_stmt->execute();
    }

    protected function createQuery(): string
    {
        $query = "
        INSERT 
            INTO price_by_interval 
            SET 
                date_start=?
                , date_end=?
                , price=?
        ";

        return $query;
    }
}
