<?php
declare(strict_types=1);
require_once __DIR__ . '/BaseDAO.php';

final class TransactionDAO extends BaseDAO
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'transactions', ['user_id', 'category_id', 'amount', 'type', 'description', 'transaction_date']);
    }
}
