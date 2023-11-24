<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class InvalidOrderByException extends ApiException {
    public function __construct(string $received) {
        parent::__construct(
            sprintf("Parameter order is expected to be ASC|DESC, '%s' received", $received),
            ResponseCode::BAD_REQUEST
        );
    }
}