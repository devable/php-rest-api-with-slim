<?php

namespace App\Helper;

use Psr\Http\Message\ResponseInterface;
use Slim\Http;

class HttpHelper {
    public static function errorResponse(string $message, int $code = Http\StatusCode::HTTP_BAD_REQUEST): ResponseInterface
    {
        return (new Http\Response())
            ->withJson([
                'status' => 'failed',
                'message' => $message
            ], $code, JSON_UNESCAPED_UNICODE);
    }

    public static function successResponse(Http\Response $response, array $body = [], int $code = Http\StatusCode::HTTP_OK): ResponseInterface
    {
        return $response->withJson(['status' => 'success'] + $body, $code, JSON_UNESCAPED_UNICODE);
    }
}
