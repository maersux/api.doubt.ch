<?php namespace UpdateApi\v1\Database;

use UpdateApi\v1\Exceptions\ApiException;
use UpdateApi\v1\Exceptions\FieldRequiredException;
use UpdateApi\v1\Exceptions\InvalidTypeException;
use UpdateApi\v1\Exceptions\NoArgumentsException;

class Insert {
    private array $query;
    private array $arguments = [];

    /** @throws ApiException */
    public function __construct(string $table, array $dbFields, string $className) {
        $this->query = [sprintf('INSERT INTO %s VALUES (NULL, :values)', $table)];

        $arguments = [];
        $data = json_decode(file_get_contents('php://input'), true);

        foreach($data as $field => $value) {
            if (!in_array($field, array_keys($dbFields))) {
                if (isset($dbFields['required']) && $dbFields['required']) {
                    throw new FieldRequiredException($className, $field);
                }

                $arguments[$field] = null;
            }

            if (gettype($value) !== $dbFields[$field]) {
                throw new InvalidTypeException($field, $dbFields[$field], gettype($value));
            }

            $arguments[$field] = htmlspecialchars($value);
        }

        if (!$arguments) throw new NoArgumentsException($className, array_keys($dbFields));

        $this->arguments = $arguments;
    }

    public function getQuery(): array {
        return $this->query;
    }

    public function getArguments(): array {
        return $this->arguments;
    }
}