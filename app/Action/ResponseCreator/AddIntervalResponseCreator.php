<?php

namespace App\Action\ResponseCreator;

use Symfony\Component\HttpFoundation\Response;

class AddIntervalResponseCreator
{
    public function createResponse(): Response
    {
        $content = 'The price for the date interval is created.';
        $response = new Response($content, Response::HTTP_CREATED);

        return $response;
    }
}