<?php namespace UpdateApi\v1\Enums;

enum RequestType: string {
    case POST = 'post';      // create
    case GET = 'get';        // read
    case PUT = 'put';        // update
    case DELETE = 'delete';  // delete
}