<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDAO.php';

final class CategoryDAO extends BaseDAO
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'categories', ['user_id', 'name', 'type']);
    }
}
