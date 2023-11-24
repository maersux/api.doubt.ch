<?php namespace UpdateApi\v1\Enums;

enum QueryStatement: string {
    case SELECT = 'SELECT';
    case INSERT = 'INSERT';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
}