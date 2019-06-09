<?php

namespace App\Action;

use App\Action\ResponseCreator\UpdateIntervalResponseCreator;
use App\Helper\DateParser\RequestDateParser;
use App\Model\PriceListEditor;
use App\Entity\Interval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateInterval implements ActionInterface
{
    /** @var RequestDateParser */
    private $requestDateParser;

    /** @var PriceListEditor */
    private $priceListEditor;

    /** @var UpdateIntervalResponseCreator */
    private $responseCreator;

    public function __construct(
        RequestDateParser $requestDateParser,
        PriceListEditor $priceListEditor,
        UpdateIntervalResponseCreator $responseCreator
    ) {
        $this->requestDateParser = $requestDateParser;
        $this->priceListEditor = $priceListEditor;
        $this->responseCreator = $responseCreator;
    }

    public function makeResponse(Request $request): Response
    {
        $currentInterval = $this->parseCurrentInterval($request);
        $newInterval = $this->parseNewInterval($request);

        $this->priceListEditor->updateInterval($currentInterval, $newInterval);
        $response = $this->responseCreator->createResponse();

        return $response;
    }

    private function parseNewInterval(Request $request): Interval
    {
        $date_start = $this->getDateStart($request, 'new_date_start');
        $date_end = $this->getDateEnd($request, 'new_date_end');
        $price = $this->getPrice($request, 'new_price');
        $id = $this->getId($request, 'new_id');

        $customer_new_interval = new Interval($date_start, $date_end, $price, $id);

        return $customer_new_interval;
    }

    private function getDateStart(Request $request, string $param_name): DateTimeImmutable
    {
        $date_start_param = $request->get($param_name);
        $date_start = $this->requestDateParser->convertStringToDate($date_start_param);

        return $date_start;
    }

    private function getDateEnd(Request $request, string $param_name): DateTimeImmutable
    {
        $date_end_param = $request->get($param_name);
        $date_end = $this->requestDateParser->convertStringToDate($date_end_param);

        return $date_end;
    }

    private function getPrice(Request $request, string $param_name): float
    {
        $price = (float)$request->get($param_name);
        return $price;
    }

    private function getId(Request $request, string $param_name): int
    {
        $id = (int)$request->get($param_name);
        return $id;
    }

    private function parseCurrentInterval(Request $request): Interval
    {
        $date_start = $this->getDateStart($request, 'current_date_start');
        $date_end = $this->getDateEnd($request, 'current_date_end');
        $price = $this->getPrice($request, 'current_price');
        $id = $this->getId($request, 'current_id');

        $current_interval = new Interval($date_start, $date_end, $price, $id);

        return $current_interval;
    }
}