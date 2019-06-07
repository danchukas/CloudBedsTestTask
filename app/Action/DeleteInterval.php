<?php

namespace App\Action;

use App\Action\ResponseCreator\DeleteIntervalResponseCreator;
use App\Helper\DateParser\RequestDateParser;
use App\Model\IntervalDestroyer;
use App\Entity\Interval;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteInterval implements ActionInterface
{
    /** @var RequestDateParser */
    private $requestDateParser;

    /** @var IntervalDestroyer */
    private $intervalDestroyer;

    /** @var DeleteIntervalResponseCreator */
    private $responseCreator;

    public function __construct(
        RequestDateParser $requestDateParser,
        IntervalDestroyer $intervalDestroyer,
        DeleteIntervalResponseCreator $responseCreator
    ) {
        $this->requestDateParser = $requestDateParser;
        $this->intervalDestroyer = $intervalDestroyer;
        $this->responseCreator = $responseCreator;
    }

    public function makeResponse(Request $request): Response
    {
        $unnecessaryInterval = $this->parseUnnecessaryInterval($request);

        $this->intervalDestroyer->deleteInterval($unnecessaryInterval);
        $response = $this->responseCreator->createResponse();

        return $response;
    }

    private function parseUnnecessaryInterval(Request $request): Interval
    {
        $date_start = $this->getDateStart($request);
        $date_end = $this->getDateEnd($request);
        $price = $this->getPrice($request);
        $id = $this->getId($request);

        $customer_new_interval = new Interval($date_start, $date_end, $price, $id);

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

    private function getId(Request $request): int
    {
        $id = (int)$request->get('id');
        return $id;
    }
}