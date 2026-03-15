<?php

namespace App\Factory;

use App\Core\Database;
use App\Core\QueryBuilder;

class QueryBuilderFactory
{
    /**
     * @var Database
     */
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    public function create(string $table): QueryBuilder
    {
        return new QueryBuilder($this->database->getConnection(), $table);
    }
}