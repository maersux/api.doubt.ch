<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\RequestType;
use UpdateApi\v1\Enums\ResponseCode;

class RequestMethodException extends ApiException {
    public function __construct(RequestType $requestType, string $route) {
        parent::__construct(
            sprintf('Request method "%s" is not intended for use for route "%s"', $requestType->value, $route),
            ResponseCode::NOT_FOUND
        );
    }
}