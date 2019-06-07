<?php

namespace App\Action\ResponseCreator;

use App\Action\ResponseCreator\ResponseSerializer\JsonSerializer;
use App\Entity\PriceList;
use Symfony\Component\HttpFoundation\Response;

class GetFullPriceListResponseCreator
{
    /** @var JsonSerializer */
    private $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createResponse(PriceList $priceList): Response
    {
        $content = $this->serializer->serialize($priceList);
        $response = new Response($content, Response::HTTP_OK);

        return $response;
    }
}