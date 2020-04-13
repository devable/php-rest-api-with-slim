<?php

namespace App\Middleware\Validation;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Exception\BadRequestException;

class AuthValidation
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$request->getParam('uid') || !$request->getParam('token')) {
            throw new BadRequestException('Проверьте входные параметры');
        }
        
        return $next($request, $response);
    }
}
