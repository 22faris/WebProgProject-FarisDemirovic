<?php
declare(strict_types=1);

final class ServiceFactory
{
    public static function forResource(string $name): EntityService
    {
        $map=[
            'users'=>[UserDAO::class,UserService::class],
            'user-settings'=>[UserSettingsDAO::class,UserSettingsService::class],
            'categories'=>[CategoryDAO::class,CategoryService::class],
            'transactions'=>[TransactionDAO::class,TransactionService::class],
            'budgets'=>[BudgetDAO::class,BudgetService::class],
        ];
        if (!isset($map[$name])) throw new ApiException(404,'API route not found.','not_found');
        $pdo=spendwisePdo();
        [$dao,$service]=$map[$name];
        return new $service(new $dao($pdo),$pdo);
    }
}
