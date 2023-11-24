<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class DatabaseInsertionException extends ApiException {
    public function __construct(string $table, array $values = []) {
        parent::__construct(
            sprintf("Failed to insert values into table %s with values '%s'",
                $table,
                implode(',', $values)
            ),
            ResponseCode::BAD_REQUEST);
    }
}