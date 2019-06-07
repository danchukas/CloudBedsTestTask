<?php

namespace App\Sql;

use App\Database;
use App\PriceListBuilder;
use App\Entity\PriceList;

class SelectFullPriceList extends StaticSqlQuery
{
    /** @var PriceListBuilder */
    private $priceListBuilder;

    public function __construct(Database $database, PriceListBuilder $priceListBuilder)
    {
        $this->priceListBuilder = $priceListBuilder;
        parent::__construct($database);
    }

    public function selectPriceList(): PriceList
    {
        $mysqli_stmt = $this->createStatement();

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
            ORDER BY date_start 
        ';

        return $query;
    }
}
