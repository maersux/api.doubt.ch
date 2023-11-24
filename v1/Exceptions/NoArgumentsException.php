<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class NoArgumentsException extends ApiException {
    public function __construct(string $table, array $values = []) {
        parent::__construct(
            sprintf("No arguments were passed for route '%s'. '%s' expected",
                $table,
                implode('\', \'', $values)
            ),
            ResponseCode::BAD_REQUEST);
    }
}