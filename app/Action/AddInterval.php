<?php

namespace App\Action;

use App\Action\ResponseCreator\AddIntervalResponseCreator;
use App\Helper\DateParser\RequestDateParser;
use App\Model\PriceListEditor;
use App\Entity\Interval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddInterval implements ActionInterface
{
    /** @var RequestDateParser */
    private $requestDateParser;

    /** @var PriceListEditor */
    private $priceListEditor;

    /** @var AddIntervalResponseCreator */
    private $responseCreator;

    public function __construct(
        RequestDateParser $requestDateParser,
        PriceListEditor $priceListEditor,
        AddIntervalResponseCreator $responseCreator
    ) {
        $this->requestDateParser = $requestDateParser;
        $this->priceListEditor = $priceListEditor;
        $this->responseCreator = $responseCreator;
    }

    public function makeResponse(Request $request): Response
    {
        $newInterval = $this->parseNewInterval($request);

        $this->priceListEditor->addInterval($newInterval);
        $response = $this->responseCreator->createResponse();

        return $response;
    }

    private function parseNewInterval(Request $request): Interval
    {
        $date_start = $this->getDateStart($request);
        $date_end = $this->getDateEnd($request);
        $price = $this->getPrice($request);

        $customer_new_interval = new Interval($date_start, $date_end, $price);

        return $customer_new_interval;
    }

    private function getDateStart(Request $request): DateTimeImmutable
    {
        $date_start_param = $request->get('date_start');
        $date_start = $this->requestDateParser->convertStringToDate($date_start_param);

        return $date_start;
    }

    private function getDateEnd(Request $request): DateTimeImmutable
    {
        $date_end_param = $request->get('date_end');
        $date_end = $this->requestDateParser->convertStringToDate($date_end_param);

        return $date_end;
    }

    private function getPrice(Request $request): float
    {
        $price = (float)$request->get('price');
        return $price;
    }
}