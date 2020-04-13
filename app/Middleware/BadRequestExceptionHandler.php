<?php

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Helper\HttpHelper;
use App\Exception\BadRequestException;

class BadRequestExceptionHandler
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            return $next($request, $response);
        } catch (BadRequestException $e) {
            return HttpHelper::errorResponse($e->getMessage());
        }
    }
}
