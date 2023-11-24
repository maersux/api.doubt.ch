<?php namespace UpdateApi\v1\Database;

use UpdateApi\v1\Exceptions\ApiException;
use UpdateApi\v1\Exceptions\InvalidTypeException;

class Parameters {
    private int $limit = 0;
    private string $sort = '';
    private string $order = 'asc';

    /** @throws ApiException */
    public function __construct() {
        foreach ($_GET as $key => $value) {
            $key = $this->toCamelCase($key);
            if (!property_exists($this, $key)) continue;

            if (gettype($this->$key) === 'integer' && !((int)$value)) {
                throw new InvalidTypeException($key, gettype($this->$key), gettype($value));
            }

            $this->$key = strtolower($value);
        }
    }

    private function toCamelCase(string $string): string {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getSort(): string {
        return $this->sort;
    }

    public function getOrder(): string {
        return $this->order;
    }
}