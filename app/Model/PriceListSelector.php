<?php


namespace App\Model;


use App\Sql\SelectFullPriceList;
use App\Entity\PriceList;

class PriceListSelector
{
    /** @var SelectFullPriceList */
    private $selectFullPriceList;

    public function __construct(SelectFullPriceList $selectFullPriceList)
    {
        $this->selectFullPriceList = $selectFullPriceList;
    }

    public function getFullPriceList(): PriceList
    {
        $priceList = $this->selectFullPriceList->selectPriceList();

        return $priceList;
    }
}