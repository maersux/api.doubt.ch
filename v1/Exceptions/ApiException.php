<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Classes\Response;
use UpdateApi\v1\Enums\ResponseCode;

class ApiException extends \Exception {
    public function __construct(string $message, ResponseCode $code) {
        $response = new Response($code, [], $message);

        parent::__construct(json_encode($response), $code->value);
    }
}