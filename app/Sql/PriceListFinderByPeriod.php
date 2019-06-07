<?php

namespace App\Sql;

use App\Database;
use App\PriceListBuilder;
use DateTimeInterface;
use App\Entity\PriceList;

class PriceListFinderByPeriod extends StaticSqlQuery
{
    /** @var PriceListBuilder */
    private $priceListBuilder;

    public function __construct(Database $database, PriceListBuilder $priceListBuilder)
    {
        $this->priceListBuilder = $priceListBuilder;
        parent::__construct($database);
    }

    public function findPriceListByPeriod(DateTimeInterface $dateFrom, DateTimeInterface $dateTo): PriceList
    {
        $mysqli_stmt = $this->createStatement();

        $formatted_date_from = $this->formatDate($dateFrom);
        $formatted_date_to = $this->formatDate($dateTo);

        $mysqli_stmt->bind_param(
            'ssss',
            $formatted_date_from,
            $formatted_date_to,
            $formatted_date_from,
            $formatted_date_to
        );

        $mysqli_stmt->execute();
        $mysqli_result = $mysqli_stmt->get_result();
        $intervals = $mysqli_result->fetch_all(MYSQLI_ASSOC);

        $intervals = $this->priceListBuilder->createFromMysqlResult($intervals);

        return $intervals;
    }

    protected function createQuery(): string
    {
        $query = '
            SELECT *
                FROM price_by_interval
            WHERE 
                date_start BETWEEN ? AND ?
                or date_end BETWEEN ? AND ?
            ORDER BY date_start
            FOR UPDATE 
        ';

        return $query;
    }
}
