<?php

return [
    'settings' => [
        'displayErrorDetails' => $dev,
        'addContentLengthHeader' => false,
        'renderer' => [
            'templatePath' => __DIR__ . '/../templates/',
        ],
        'logger' => [
            'name' => 'php-rest-api-with-slim',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'db' => 'pdo-mysql://' . ($_ENV['APP_DB'] ?? 'prod_user:prod_pass@prod_address:prod_port') . '/crm?charset=utf8'
    ]
];
