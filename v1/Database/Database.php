<?php namespace UpdateApi\v1\Database;

use PDO;
use UpdateApi\Config;

class Database {
    private PDO $connection;

    public function __construct() {
        try {
            $this->connection = new PDO(
                sprintf('mysql:host=%s; dbname=%s; charset=utf8', Config::DB_HOST, Config::DB_NAME),
                Config::DB_USER,
                Config::DB_PASSWORD,
                [
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]
            );
        } catch (\PDOException $exception) {
            die(sprintf('Could not connect to the database: %s', $exception));
        }
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}