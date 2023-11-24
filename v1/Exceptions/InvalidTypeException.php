<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class InvalidTypeException extends ApiException {
    public function __construct(string $parameter, string $expected, string $received) {
        parent::__construct(
            sprintf('Parameter %s is expected to be of type %s, %s received', $parameter, $expected, $received),
            ResponseCode::BAD_REQUEST
        );
    }
}