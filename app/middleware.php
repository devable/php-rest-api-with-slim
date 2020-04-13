<?php

$app->add(new \App\Middleware\BadRequestExceptionHandler());
$app->add(new \App\Middleware\CustomExceptionHandler());
