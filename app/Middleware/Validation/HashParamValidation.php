<?php

namespace App\Middleware\Validation;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Exception\BadRequestException;

class HashParamValidation
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$request->getParam('hash')) {
            throw new BadRequestException('Проверьте входные параметры');
        }

        return $next($request, $response);
    }
}
