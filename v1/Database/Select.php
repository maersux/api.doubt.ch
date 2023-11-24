<?php namespace UpdateApi\v1\Database;

use UpdateApi\v1\Exceptions\InvalidFieldException;
use UpdateApi\v1\Exceptions\InvalidOrderByException;
use UpdateApi\v1\Exceptions\InvalidTypeException;

class Select {
    private array $query;
    private array $arguments = [];

    /**
     * @throws InvalidFieldException
     * @throws InvalidOrderByException
     * @throws InvalidTypeException
     */
    public function __construct(string $table, array $dbFields, string $className) {
        $this->query = [sprintf('SELECT * FROM `%s`', $table)];

        foreach ($dbFields as $field => $properties) {
            $parameter = $_GET[$field] ?? null;
            if (is_null($parameter)) continue;

            switch ($properties['type']) {
                case 'string':
                    $this->addStringToQuery($field, $parameter);
                    break;

                case 'integer':
                    $this->addIntegerToQuery($properties, $field, $parameter);
                    break;
            }
        }

        $queryParams = new Parameters();
        if ($queryParams->getSort()) {
            $sort = match($queryParams->getOrder()) {
                'asc' => 'ASC',
                'desc' => 'DESC',
                default => ''
            };

            if (!$sort) {
                throw new InvalidOrderByException($queryParams->getOrder());
            }

            if (!in_array($queryParams->getSort(), array_keys($dbFields))) {
                throw new InvalidFieldException($className, $queryParams->getSort());
            }

            $this->query[] = sprintf('ORDER BY %s %s', $queryParams->getSort(), $queryParams->getOrder());
        }

        if ($queryParams->getLimit()) {
            $this->query[] = sprintf('LIMIT %s', $queryParams->getLimit());
        }
    }


    /**
     * @throws InvalidTypeException
     */
    private function addIntegerToQuery(array $properties, string $field, string $parameter): void {
        if (!preg_match('/^[<>]?\d+$/', $parameter)) {
            throw new InvalidTypeException($field, $properties['type'], gettype($parameter));
        }

        $comparison = '=';
        if ($parameter[0] === '<') $comparison = '<';
        if ($parameter[0] === '>') $comparison = '>';

        $this->query[] = sprintf('WHERE %s %s :%s', $field, $comparison, $field);
        $this->arguments[":$field"] = str_replace(['<', '>'], '', $parameter);
    }

    private function addStringToQuery(string $field, string $parameter): void {
        $this->query[] = sprintf('WHERE %s LIKE :%s', $field, $field);
        $this->arguments[":$field"] = "%$parameter%";
    }



    public function getQuery(): array {
        return $this->query;
    }

    public function getArguments(): array {
        return $this->arguments;
    }
}