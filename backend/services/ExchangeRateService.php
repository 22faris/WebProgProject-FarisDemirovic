<?php
declare(strict_types=1);
final class ExchangeRateService
{
    public function convert(string $from,string $to,float $amount): array
    {
        $from=strtoupper($from); $to=strtoupper($to);
        if (!preg_match('/^[A-Z]{3}$/',$from)||!preg_match('/^[A-Z]{3}$/',$to)) throw new ApiException(400,'Currency codes must be three-letter ISO currency codes.');
        if (!is_finite($amount)||$amount<=0||$amount>1000000000) throw new ApiException(400,'amount must be a positive number no greater than 1,000,000,000.');
        $currencyContext=stream_context_create(['http'=>['timeout'=>7,'ignore_errors'=>true,'header'=>"Accept: application/json\r\nUser-Agent: SpendWise/1.0\r\n"]]);
        $currencyBody=@file_get_contents('https://api.frankfurter.dev/v2/currencies',false,$currencyContext);
        $currencyStatus=0;
        foreach ($http_response_header??[] as $header) if (preg_match('/^HTTP\/\S+\s+(\d+)/',$header,$m)) $currencyStatus=(int)$m[1];
        if ($currencyBody===false||$currencyStatus<200||$currencyStatus>=300) throw new ApiException(503,'Currency information is temporarily unavailable. Please try again later.','external_service_unavailable');
        $currencies=json_decode($currencyBody,true);
        $supported=is_array($currencies)?array_column($currencies,'iso_code'):[];
        if (!in_array($from,$supported,true)||!in_array($to,$supported,true)) throw new ApiException(400,'One or both currency codes are not supported by Frankfurter.');
        if ($from===$to) return ['amount'=>$amount,'from'=>$from,'to'=>$to,'rate'=>1.0,'converted_amount'=>round($amount,2),'date'=>date('Y-m-d')];
        $url='https://api.frankfurter.dev/v2/rates?'.http_build_query(['base'=>$from,'quotes'=>$to]);
        $context=stream_context_create(['http'=>['timeout'=>7,'ignore_errors'=>true,'header'=>"Accept: application/json\r\nUser-Agent: SpendWise/1.0\r\n"]]);
        $body=@file_get_contents($url,false,$context); $status=0;
        foreach ($http_response_header??[] as $header) if (preg_match('/^HTTP\/\S+\s+(\d+)/',$header,$m)) $status=(int)$m[1];
        if ($body===false||$status<200||$status>=300) throw new ApiException(503,'Exchange rates are temporarily unavailable. Please try again later.','external_service_unavailable');
        $rows=json_decode($body,true); if (!is_array($rows)||!isset($rows[0]['rate'],$rows[0]['date'])||!is_numeric($rows[0]['rate'])) throw new ApiException(503,'Exchange rates could not be read from the rate provider.','external_service_unavailable');
        $rate=(float)$rows[0]['rate']; return ['amount'=>$amount,'from'=>$from,'to'=>$to,'rate'=>$rate,'converted_amount'=>round($amount*$rate,2),'date'=>$rows[0]['date']];
    }
}
