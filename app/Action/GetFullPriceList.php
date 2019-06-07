<?php

namespace App\Action;

use App\Action\ResponseCreator\GetFullPriceListResponseCreator;
use App\Model\PriceListSelector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetFullPriceList implements ActionInterface
{
    /** @var PriceListSelector */
    private $priceListSelector;

    /** @var GetFullPriceListResponseCreator */
    private $responseCreator;

    public function __construct(PriceListSelector $priceListSelector, GetFullPriceListResponseCreator $responseCreator)
    {
        $this->priceListSelector = $priceListSelector;
        $this->responseCreator = $responseCreator;
    }

    public function makeResponse(Request $request): Response
    {
        $priceList = $this->priceListSelector->getFullPriceList();

        $response = $this->responseCreator->createResponse($priceList);

        return $response;
    }
}