<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class RouteException extends ApiException {
    public function __construct(string $path = '') {
        parent::__construct(
            sprintf("No route configured for path '%s'", $path),
            ResponseCode::NOT_FOUND
        );
    }
}
