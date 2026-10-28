<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDAO.php';

final class UserDAO extends BaseDAO
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'users', ['full_name', 'email', 'password_hash']);
    }
}
