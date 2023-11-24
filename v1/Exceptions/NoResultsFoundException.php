<?php namespace UpdateApi\v1\Exceptions;

use UpdateApi\v1\Enums\ResponseCode;

class NoResultsFoundException extends ApiException {
    public function __construct(int $id = 0) {
        $message = 'No entries found.';
        if ($id) $message = sprintf('No entry with ID %d found.', $id);

        parent::__construct($message, ResponseCode::NOT_FOUND);
    }
}