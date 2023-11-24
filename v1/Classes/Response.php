<?php namespace UpdateApi\v1\Classes;

use UpdateApi\v1\Enums\ResponseCode;

class Response {
    public int $status;
    public array $data;
    public ?string $message;

    public function __construct(ResponseCode $status, array $data = [], ?string $message = null) {
        $this->status = $status->value;
        $this->data = $data;
        $this->message = $message;
    }
}