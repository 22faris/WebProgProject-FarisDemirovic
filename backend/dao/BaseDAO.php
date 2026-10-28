<?php
declare(strict_types=1);

abstract class BaseDAO
{
    protected PDO $pdo;
    protected string $table;
    /** @var list<string> */
    protected array $writableColumns;

    /** @param list<string> $writableColumns */
    protected function __construct(PDO $pdo, string $table, array $writableColumns)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->writableColumns = $writableColumns;
    }

    /** Insert a row and return its generated ID. */
    public function create(array $data): int
    {
        $data = $this->validateData($data);
        $columns = array_keys($data);
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->table,
            implode(', ', array_map(static fn(string $column): string => "`{$column}`", $columns)),
            implode(', ', array_map(static fn(string $column): string => ":{$column}", $columns))
        );
        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    /** Return one row or null when its ID does not exist. */
    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE id = :id");
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    /** @return list<array<string, mixed>> */
    public function findAll(): array
    {
        $statement = $this->pdo->query("SELECT * FROM `{$this->table}` ORDER BY id");
        return $statement->fetchAll();
    }

    /** Return false if no row exists; PDO errors are allowed to reach the caller. */
    public function update(int $id, array $data): bool
    {
        $data = $this->validateData($data);
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "`{$column}` = :{$column}";
        }
        $data['__id'] = $id;
        $statement = $this->pdo->prepare(
            "UPDATE `{$this->table}` SET " . implode(', ', $sets) . ' WHERE id = :__id'
        );
        $statement->execute($data);
        return $statement->rowCount() > 0 || $this->findById($id) !== null;
    }

    /** Return false if no row exists. */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
        $statement->execute(['id' => $id]);
        return $statement->rowCount() > 0;
    }

    private function validateData(array $data): array
    {
        if ($data === []) {
            throw new InvalidArgumentException('At least one field is required.');
        }
        foreach (array_keys($data) as $column) {
            if (!is_string($column) || !in_array($column, $this->writableColumns, true)) {
                throw new InvalidArgumentException('Unsupported field supplied to DAO.');
            }
        }
        return $data;
    }
}
