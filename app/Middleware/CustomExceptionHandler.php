<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Helper\HttpHelper;
use App\Exception\CustomException;

class CustomExceptionHandler
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            return $next($request, $response);
        } catch (CustomException $e) {
            return HttpHelper::errorResponse($e->getMessage());
        }
    }
}
