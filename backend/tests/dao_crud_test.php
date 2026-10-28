<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/UserSettingsDAO.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';
require_once __DIR__ . '/../dao/TransactionDAO.php';
require_once __DIR__ . '/../dao/BudgetDAO.php';

function expect(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

try {
    $pdo = spendwisePdo();
    if (!$pdo->beginTransaction()) {
        throw new RuntimeException('Could not start rollback-only test transaction.');
    }

    $users = new UserDAO($pdo);
    $settings = new UserSettingsDAO($pdo);
    $categories = new CategoryDAO($pdo);
    $transactions = new TransactionDAO($pdo);
    $budgets = new BudgetDAO($pdo);

    $suffix = bin2hex(random_bytes(6));
    $userId = $users->create([
        'full_name' => 'CRUD Test User',
        'email' => "crud-{$suffix}@example.invalid",
        'password_hash' => '!test-only-no-password!',
    ]);
    expect($userId > 0 && $users->findById($userId) !== null, 'User create/read failed.');
    expect(count($users->findAll()) > 0, 'User list read failed.');
    expect($users->update($userId, ['full_name' => 'CRUD Test User Updated']), 'User update failed.');

    expect($settings->create(['user_id' => $userId, 'preferred_currency' => 'BAM']) === $userId, 'Settings create failed.');
    expect($settings->findById($userId)['preferred_currency'] === 'BAM', 'Settings read failed.');
    expect(count($settings->findAll()) > 0, 'Settings list read failed.');
    expect($settings->update($userId, ['preferred_currency' => 'EUR']), 'Settings update failed.');

    $categoryId = $categories->create(['user_id' => $userId, 'name' => 'Test Food', 'type' => 'expense']);
    expect($categories->findById($categoryId) !== null, 'Category create/read failed.');
    expect(count($categories->findAll()) > 0, 'Category list read failed.');
    expect($categories->update($categoryId, ['name' => 'Updated Food']), 'Category update failed.');

    $transactionId = $transactions->create([
        'user_id' => $userId,
        'category_id' => $categoryId,
        'amount' => '12.50',
        'type' => 'expense',
        'description' => 'Test groceries',
        'transaction_date' => date('Y-m-d'),
    ]);
    expect($transactions->findById($transactionId) !== null, 'Transaction create/read failed.');
    expect(count($transactions->findAll()) > 0, 'Transaction list read failed.');
    expect($transactions->update($transactionId, ['description' => 'Updated groceries']), 'Transaction update failed.');

    $budgetId = $budgets->create([
        'user_id' => $userId,
        'category_id' => $categoryId,
        'amount_limit' => '250.00',
        'month' => date('Y-m-01'),
    ]);
    expect($budgets->findById($budgetId) !== null, 'Budget create/read failed.');
    expect(count($budgets->findAll()) > 0, 'Budget list read failed.');
    expect($budgets->update($budgetId, ['amount_limit' => '300.00']), 'Budget update failed.');

    expect($transactions->delete($transactionId), 'Transaction delete failed.');
    expect($transactions->findById($transactionId) === null, 'Deleted transaction still exists.');
    expect($budgets->delete($budgetId), 'Budget delete failed.');
    expect($categories->delete($categoryId), 'Category delete failed.');
    expect($settings->delete($userId), 'Settings delete failed.');
    expect($users->delete($userId), 'User delete failed.');
    expect(!$users->delete($userId), 'Deleting a missing user should return false.');
    expect($users->findById(-999999) === null, 'Missing user lookup should return null.');

    $pdo->rollBack();
    echo "PASS: CRUD demonstrated for users, user_settings, categories, transactions, and budgets.\n";
    echo "PASS: Missing-record handling and transaction rollback verified.\n";
} catch (Throwable $error) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . "\n");
    exit(1);
}
