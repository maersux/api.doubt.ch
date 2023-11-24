<?php namespace UpdateApi\v1\Endpoints;

use UpdateApi\v1\Classes\Endpoint;
use UpdateApi\v1\Enums\RequestType;
use UpdateApi\v1\Exceptions\ApiException;
use UpdateApi\v1\Exceptions\RequestMethodException;

class Families extends Endpoint {
    private const ALLOWED_REQUEST_TYPES = [
        RequestType::GET,
        RequestType::POST
    ];

    private const FIELDS = [
        'id' => [
            'type' => 'integer'
        ],
        'name' => [
            'type' => 'string',
            'required' => true
        ],
        'size' => [
            'type' => 'integer',
            'required' => true
        ]
    ];

    /**
     * @throws ApiException
     */
    public function __construct() {
        parent::__construct(self::FIELDS);

        if (!in_array($this->requestType, self::ALLOWED_REQUEST_TYPES)) {
            throw new RequestMethodException($this->requestType, get_class($this));
        }

        $requestType = $this->requestType->value;

        $controller = method_exists($this, $requestType) ? $this : parent::class;
        $this->response = call_user_func([$controller, $this->requestType->value]);
    }
}