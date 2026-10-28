<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDAO.php';

final class UserSettingsDAO extends BaseDAO
{
    protected string $table = 'user_settings';
    protected array $writableColumns = ['user_id', 'preferred_currency'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM user_settings WHERE user_id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    public function update(int $id, array $data): bool
    {
        if (array_key_exists('user_id', $data)) {
            throw new InvalidArgumentException('A settings row user_id cannot be changed.');
        }
        if ($data === []) {
            throw new InvalidArgumentException('At least one field is required.');
        }
        foreach (array_keys($data) as $column) {
            if (!in_array($column, $this->writableColumns, true)) {
                throw new InvalidArgumentException('Unsupported field supplied to DAO.');
            }
        }
        if (!array_key_exists('preferred_currency', $data)) {
            throw new InvalidArgumentException('preferred_currency is required for a settings update.');
        }
        $statement = $this->pdo->prepare('UPDATE user_settings SET preferred_currency = :preferred_currency WHERE user_id = :id');
        $statement->execute(['preferred_currency' => $data['preferred_currency'], 'id' => $id]);
        return $statement->rowCount() > 0 || $this->findById($id) !== null;
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM user_settings WHERE user_id = :id');
        $statement->execute(['id' => $id]);
        return $statement->rowCount() > 0;
    }

    public function create(array $data): int
    {
        if (!isset($data['user_id'], $data['preferred_currency']) || count($data) !== 2) {
            throw new InvalidArgumentException('user_id and preferred_currency are required.');
        }
        $statement = $this->pdo->prepare('INSERT INTO user_settings (user_id, preferred_currency) VALUES (:user_id, :preferred_currency)');
        $statement->execute($data);
        return (int) $data['user_id'];
    }

    public function findAll(): array
    {
        return $this->pdo->query('SELECT * FROM user_settings ORDER BY user_id')->fetchAll();
    }
}
