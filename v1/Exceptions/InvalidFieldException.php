<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class InvalidFieldException extends ApiException {
    public function __construct(string $route, string $field) {
        parent::__construct(
            sprintf("Field '%s' is not allowed for route '%s'", $field, $route),
            ResponseCode::BAD_REQUEST
        );
    }
}