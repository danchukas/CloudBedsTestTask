<?php

namespace App\Action\ResponseCreator;

use Symfony\Component\HttpFoundation\Response;

class UpdateIntervalResponseCreator
{
    public function createResponse(): Response
    {
        $content = 'Interval updated.';
        $response = new Response($content, Response::HTTP_OK);

        return $response;
    }
}