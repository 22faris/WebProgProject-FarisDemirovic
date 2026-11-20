<?php
declare(strict_types=1);
final class ApiException extends RuntimeException
{
    public function __construct(public readonly int $status, string $message, public readonly string $errorCode = 'invalid_request')
    { parent::__construct($message); }
}
