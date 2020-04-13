<?php

use Slim\Container;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

// view renderer
$container['renderer'] = function (Container $c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['templatePath']);
};

// monolog
$container['logger'] = function (Container $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// storages
$container['db'] = function (Container $c) {
    return DriverManager::getConnection(
        ['url' => $c->get('settings')['db']],
        new Configuration
    );
};

$container->register(new \App\ServiceProvider\CRMStorage());
