<?php

namespace app\middleware;

use pew\View;
use pew\response\JsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

class ReturnJson
{
    public function after(\pew\View $view)
    {
        $response = new SymfonyJsonResponse($view->getData());

        return new JsonResponse($response);
    }
}
