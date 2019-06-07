<?php


namespace App\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ActionInterface
{
    public function makeResponse(Request $request): Response;
}