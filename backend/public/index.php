<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/UserSettingsDAO.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';
require_once __DIR__ . '/../dao/TransactionDAO.php';
require_once __DIR__ . '/../dao/BudgetDAO.php';
foreach (['ApiException','EntityService','UserService','UserSettingsService','CategoryService','TransactionService','BudgetService','ExchangeRateService','ServiceFactory'] as $file) require_once __DIR__ . '/../services/' . $file . '.php';

Flight::map('error', function (Throwable $error): void {
    if ($error instanceof ApiException) { Flight::json(['error'=>['code'=>$error->errorCode,'message'=>$error->getMessage()]],$error->status); return; }
    if ($error instanceof PDOException && (string)$error->getCode()==='23000') { Flight::json(['error'=>['code'=>'constraint_violation','message'=>'The request conflicts with an existing record or database relationship.']],400); return; }
    error_log('SpendWise API error: ' . get_class($error) . ' — ' . $error->getMessage());
    Flight::json(['error'=>['code'=>'server_error','message'=>'An unexpected server error occurred.']],500);
});
Flight::map('notFound', function (): void {
    Flight::json(['error'=>['code'=>'not_found','message'=>'API route not found.']],404);
});
Flight::map('methodNotFound', function (): void {
    Flight::json(['error'=>['code'=>'method_not_allowed','message'=>'HTTP method is not supported for this route.']],404);
});

function apiBody(): array
{
    $raw=Flight::request()->getBody();
    try { $data=json_decode($raw,true,512,JSON_THROW_ON_ERROR); }
    catch (JsonException) { throw new ApiException(400,'Request body must be valid JSON.','malformed_json'); }
    if (!is_array($data)||array_is_list($data)) throw new ApiException(400,'Request body must be a JSON object.');
    return $data;
}
function apiId(string $raw): int
{
    $id=filter_var($raw,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    if ($id===false) throw new ApiException(400,'ID must be a positive integer.');
    return $id;
}
function apiRun(callable $operation): void
{
    try { $operation(); }
    catch (Throwable $error) { Flight::error($error); }
}

require __DIR__ . '/../routes/api.php';
Flight::start();
