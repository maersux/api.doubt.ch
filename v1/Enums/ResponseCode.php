<?php namespace UpdateApi\v1\Enums;

enum ResponseCode: int {
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case NOT_FOUND = 404;
    case NOT_ALLOWED = 405;
}