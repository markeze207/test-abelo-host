<?php

namespace App\Core;

use PDO;
use PDOException;

class QueryBuilder
{
    private PDO $db;
    private string $table;
    private array $select = ['*'];
    private array $joins = [];
    private array $wheres = [];
    private array $params = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private ?string $groupBy = null;
    private ?string $having = null;

    public function __construct(PDO $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function select(array $fields): self
    {
        $this->select = $fields;
        return $this;
    }

    public function addSelect(string $field): self
    {
        $this->select[] = $field;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "INNER JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function where(string $field, string $operator, $value): self
    {
        $this->wheres[] = [
            'type'   => 'basic',
            'sql'    => "$field $operator ?",
            'params' => [$value],
        ];
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $field, string $operator, $value): self
    {
        $this->wheres[] = [
            'type'   => 'or',
            'sql'    => "$field $operator ?",
            'params' => [$value],
        ];
        $this->params[] = $value;
        return $this;
    }

    public function whereIn(string $field, array $values): self
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = [
            'type'   => 'basic',
            'sql'    => "$field IN ($placeholders)",
            'params' => $values,
        ];
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function whereNotIn(string $field, array $values): self
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = [
            'type'   => 'basic',
            'sql'    => "$field NOT IN ($placeholders)",
            'params' => $values,
        ];
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function whereBetween(string $field, $value1, $value2): self
    {
        $this->wheres[] = [
            'type'   => 'basic',
            'sql'    => "$field BETWEEN ? AND ?",
            'params' => [$value1, $value2],
        ];
        $this->params[] = $value1;
        $this->params[] = $value2;
        return $this;
    }

    public function whereNull(string $field): self
    {
        $this->wheres[] = ['type' => 'basic', 'sql' => "$field IS NULL", 'params' => []];
        return $this;
    }

    public function whereNotNull(string $field): self
    {
        $this->wheres[] = ['type' => 'basic', 'sql' => "$field IS NOT NULL", 'params' => []];
        return $this;
    }

    public function whereRaw(string $condition, array $params = []): self
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => $condition, 'params' => $params];

        if (!empty($params)) {
            $this->params = array_merge($this->params, $params);
        }

        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy[] = "$field $direction";
        return $this;
    }

    public function orderByDesc(string $field): self
    {
        $this->orderBy[] = "$field DESC";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function forPage(int $page, int $perPage = 10): self
    {
        $this->limit  = $perPage;
        $this->offset = ($page - 1) * $perPage;
        return $this;
    }

    public function groupBy(string $field): self
    {
        $this->groupBy = $field;
        return $this;
    }

    public function having(string $condition): self
    {
        $this->having = $condition;
        return $this;
    }

    public function get(): array
    {
        $sql = $this->buildSelect();

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($this->params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return [];
        }
    }

    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function value(string $field)
    {
        $this->select = [$field];
        $result = $this->first();
        return $result[$field] ?? null;
    }

    public function pluck(string $field): array
    {
        $this->select = [$field];
        $results = $this->get();
        return array_column($results, $field);
    }

    public function count(): int
    {
        $sql = $this->buildCount();

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($this->params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return 0;
        }
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function insert(array $data): int
    {
        $fields       = array_keys($data);
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $sql          = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ($placeholders)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($data));
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return 0;
        }
    }

    public function update(array $data): int
    {
        $fields = [];
        $params = [];

        foreach ($data as $field => $value) {
            $fields[] = "$field = ?";
            $params[] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(',', $fields);

        if (!empty($this->wheres)) {
            $sql    .= " WHERE " . $this->buildWhereClause();
            $params  = array_merge($params, $this->params);
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return 0;
        }
    }

    public function increment(string $column, int $amount = 1): int
    {
        $sql = "UPDATE {$this->table} SET $column = $column + $amount";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($this->params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return 0;
        }
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($this->params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError($e, $sql);
            return 0;
        }
    }

    public function reset(): self
    {
        $this->select  = ['*'];
        $this->joins   = [];
        $this->wheres  = [];
        $this->params  = [];
        $this->orderBy = [];
        $this->limit   = null;
        $this->offset  = null;
        $this->groupBy = null;
        $this->having  = null;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildSelect(): string
    {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        if ($this->groupBy) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        if ($this->having) {
            $sql .= " HAVING {$this->having}";
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    private function buildCount(): string
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->buildWhereClause();
        }

        return $sql;
    }

    private function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $clauses    = [];
        $firstWhere = true;

        foreach ($this->wheres as $where) {
            if ($firstWhere) {
                $clauses[]  = $where['sql'];
                $firstWhere = false;
            } elseif ($where['type'] === 'or') {
                $clauses[] = "OR " . $where['sql'];
            } else {
                $clauses[] = "AND " . $where['sql'];
            }
        }

        return implode(' ', $clauses);
    }

    private function handleError(PDOException $e, string $sql): void
    {
        error_log("Database error: " . $e->getMessage());
        error_log("SQL: " . $sql);
        error_log("Params: " . json_encode($this->params));

        if (($_ENV['APP_DEBUG'] ?? true) && PHP_SAPI !== 'cli') {
            echo "<pre style='background:#f8d7da;color:#721c24;padding:10px;border:1px solid #f5c6cb;margin:10px'>";
            echo "<strong>Database Error:</strong> " . htmlspecialchars($e->getMessage()) . "\n";
            echo "<strong>SQL:</strong> " . htmlspecialchars($sql) . "\n";
            echo "<strong>Params:</strong> " . htmlspecialchars(print_r($this->params, true));
            echo "</pre>";
        }
    }
}