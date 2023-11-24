<?php namespace UpdateApi\v1\Classes;

use UpdateApi\v1\Database\Query;
use UpdateApi\v1\Enums\RequestType;
use UpdateApi\v1\Enums\ResponseCode;
use UpdateApi\v1\Exceptions\InvalidFieldException;
use UpdateApi\v1\Exceptions\InvalidOrderByException;
use UpdateApi\v1\Exceptions\NoArgumentsException;
use UpdateApi\v1\Exceptions\NoResultsFoundException;
use UpdateApi\v1\Exceptions\InvalidTypeException;

class Endpoint {
    protected string $uri;
    protected string $className;
    protected array $dbFields;
    protected Response $response;

    protected RequestType $requestType;

    public function __construct(array $dbFields) {
        $uriParts = explode('/', $_SERVER['REQUEST_URI']);

        $this->uri = $uriParts[1];
        $this->dbFields = $dbFields;

        $this->requestType = match($_SERVER['REQUEST_METHOD']) {
            'GET' => RequestType::GET,
            'POST' => RequestType::POST,
            'DELETE' => RequestType::DELETE,
            'PUT' => RequestType::PUT
        };

        $classWithNamespace = explode('\\', get_class($this));
        $this->className = strtolower(end($classWithNamespace));
    }

    /**
     * @throws NoResultsFoundException
     * @throws InvalidOrderByException
     * @throws InvalidFieldException
     * @throws InvalidTypeException
     */
    protected function get(): Response {
        $query = new Query($this->className);

        $query->prepareSelect($this->dbFields, $this->className);

        return $query->execute();
    }

    /**
     * @throws InvalidFieldException|NoArgumentsException|InvalidTypeException
     * @throws NoResultsFoundException
     */
    protected function post(): Response {
        $query = new Query($this->className);

        $query->prepareInsert($this->dbFields, $this->className);

        return $query->execute();
    }


    protected function delete(): Response {
        return new Response(ResponseCode::NOT_FOUND, [], 'Not yet implemented');
    }

    protected function put(): Response {
        return new Response(ResponseCode::NOT_FOUND, [], 'Not yet implemented');
    }

    public function getResponse(): Response {
        return $this->response;
    }
}