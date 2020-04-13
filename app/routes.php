<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('/news', function () use ($app) {
    return new App\Controller\NewsfeedController($app, 'news');
});

$app->group('/articles', function () use ($app) {
    return new App\Controller\NewsfeedController($app, 'articles');
});

$app->group('/auth', function () use ($app) {
    return new App\Controller\AuthController($app);
});

$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'index.phtml', $args);
});
