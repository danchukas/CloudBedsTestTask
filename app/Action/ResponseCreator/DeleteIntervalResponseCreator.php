<?php

namespace App\Action\ResponseCreator;

use Symfony\Component\HttpFoundation\Response;

class DeleteIntervalResponseCreator
{
    public function createResponse(): Response
    {
        $content = 'The price for the date interval is removed.';
        $response = new Response($content, Response::HTTP_OK);

        return $response;
    }
}