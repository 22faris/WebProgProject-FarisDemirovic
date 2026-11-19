<?php
declare(strict_types=1);

require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/UserSettingsDAO.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';
require_once __DIR__ . '/../dao/TransactionDAO.php';
require_once __DIR__ . '/../dao/BudgetDAO.php';
require_once __DIR__ . '/../services/ApiException.php';
require_once __DIR__ . '/../services/EntityService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/UserSettingsService.php';
require_once __DIR__ . '/../services/CategoryService.php';
require_once __DIR__ . '/../services/TransactionService.php';
require_once __DIR__ . '/../services/BudgetService.php';

function expectBadRequest(callable $operation, string $case): void
{
    try {
        $operation();
    } catch (ApiException $error) {
        if ($error->status === 400) return;
        throw new RuntimeException("{$case} returned HTTP {$error->status}, expected 400.");
    }
    throw new RuntimeException("{$case} was accepted, expected HTTP 400.");
}

$pdo = new PDO('sqlite::memory:');
$cases = [
    'user required fields' => fn() => (new UserService(new UserDAO($pdo), $pdo))->create([]),
    'user invalid email' => fn() => (new UserService(new UserDAO($pdo), $pdo))->create(['full_name'=>'Test User','email'=>'invalid','password'=>'safe-test-password']),
    'user short password' => fn() => (new UserService(new UserDAO($pdo), $pdo))->create(['full_name'=>'Test User','email'=>'test@example.com','password'=>'short']),
    'settings required fields' => fn() => (new UserSettingsService(new UserSettingsDAO($pdo), $pdo))->create([]),
    'category required fields' => fn() => (new CategoryService(new CategoryDAO($pdo), $pdo))->create([]),
    'transaction required fields' => fn() => (new TransactionService(new TransactionDAO($pdo), $pdo))->create([]),
    'budget required fields' => fn() => (new BudgetService(new BudgetDAO($pdo), $pdo))->create([]),
];

foreach ($cases as $label => $operation) expectBadRequest($operation, $label);
echo 'PASS: ' . count($cases) . " service input-validation cases returned HTTP 400 without a database server.\n";
