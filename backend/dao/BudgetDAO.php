<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDAO.php';

final class BudgetDAO extends BaseDAO
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'budgets', ['user_id', 'category_id', 'amount_limit', 'month']);
    }
}
