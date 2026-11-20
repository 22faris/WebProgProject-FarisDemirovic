<?php
declare(strict_types=1);

$resources=['users','user-settings','categories','transactions','budgets'];
foreach ($resources as $resource) {
    $base='/api/' . $resource;
    Flight::route('GET ' . $base, function () use ($resource): void { apiRun(fn()=>Flight::json(['data'=>ServiceFactory::forResource($resource)->all()])); });
    Flight::route('GET ' . $base . '/@id', function (string $id) use ($resource): void { apiRun(function () use ($resource,$id): void { $id=apiId($id); Flight::json(['data'=>ServiceFactory::forResource($resource)->get($id)]); }); });
    Flight::route('POST ' . $base, function () use ($resource): void { apiRun(function () use ($resource): void { $body=apiBody(); $created=ServiceFactory::forResource($resource)->create($body); Flight::response()->header('Location',rtrim(Flight::request()->url,'/') . '/' . ($created['id']??$created['user_id'])); Flight::json(['data'=>$created],201); }); });
    foreach (['PUT','PATCH'] as $method) Flight::route($method . ' ' . $base . '/@id', function (string $id) use ($resource): void { apiRun(function () use ($resource,$id): void { $id=apiId($id); $body=apiBody(); Flight::json(['data'=>ServiceFactory::forResource($resource)->update($id,$body)]); }); });
    Flight::route('DELETE ' . $base . '/@id', function (string $id) use ($resource): void { apiRun(function () use ($resource,$id): void { $id=apiId($id); ServiceFactory::forResource($resource)->delete($id); Flight::json(['message'=>'Record deleted.']); }); });
}

Flight::route('GET /api/exchange/convert', function (): void {
    apiRun(function (): void {
        $query=Flight::request()->query;
        foreach (['from','to','amount'] as $required) if (!isset($query[$required])) throw new ApiException(400,"Missing required query parameter: {$required}.");
        if (!is_numeric($query['amount'])) throw new ApiException(400,'amount must be a number.');
        Flight::json(['data'=>(new ExchangeRateService())->convert((string)$query['from'],(string)$query['to'],(float)$query['amount'])]);
    });
});
