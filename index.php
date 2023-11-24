<?php namespace UpdateApi;

use Exception;
use UpdateApi\v1\Classes\RouteHandler;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    $router = new RouteHandler;
    echo $router->processRequest();
}
catch (Exception $exception) {
    http_response_code($exception->getCode());
    echo $exception->getMessage();
}