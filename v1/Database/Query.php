<?php namespace UpdateApi\v1\Database;

use PDO;
use UpdateApi\v1\Classes\Response;
use UpdateApi\v1\Enums\QueryStatement;
use UpdateApi\v1\Enums\ResponseCode;
use UpdateApi\v1\Exceptions\ApiException;
use UpdateApi\v1\Exceptions\NoResultsFoundException;

class Query {
    private string $table;
    private PDO $connection;

    private array $query = [];
    private array $arguments = [];

    private QueryStatement $statement;

    public function __construct(string $table) {
        $database = new Database();
        $this->connection = $database->getConnection();
        $this->table = $table;
    }

    /**
     * @throws ApiException
     */
    public function prepareSelect(array $dbFields, string $className): void {
        $select = new Select($this->table, $dbFields, $className);

        $this->statement = QueryStatement::SELECT;

        $this->query = $select->getQuery();
        $this->arguments = $select->getArguments();
    }

    /**
     * @throws ApiException
     */
    public function prepareInsert(array $dbFields, string $className): void {
        $insert = new Insert($this->table, $dbFields, $className);

        $this->statement = QueryStatement::INSERT;

        $this->query = $insert->getQuery();
        $this->arguments = $insert->getArguments();
    }

    /**
     * @throws NoResultsFoundException
     */
    public function execute(): Response {
        $statement = $this->connection->prepare(implode(' ', $this->query));
        $statement->execute($this->arguments);

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!count($results)) {
            throw new NoResultsFoundException($this->arguments[':id'] ?? 0);
        }

        return new Response(ResponseCode::OK, $results);
    }
}