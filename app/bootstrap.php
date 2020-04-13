<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$dev = getenv('APP_DEV') === 'true' || $_SERVER['HTTP_HOST'] !== 'prod_address';

if ($dev) {
    $dotenv = new \Symfony\Component\Dotenv\Dotenv();
    $dotenv->load(__DIR__ . '/../.env');
}

$settings = require_once __DIR__ . '/config.php';
$app = new \Slim\App($settings);

/** @var  $container \Slim\Container */
$container = $app->getContainer();

require_once __DIR__ . '/dependencies.php';
require_once __DIR__ . '/middleware.php';
require_once __DIR__ . '/routes.php';

return $app;
