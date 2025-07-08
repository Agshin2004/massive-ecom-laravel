<?php

namespace App\Exceptions;

class ForbiddenException extends \Exception
{
    public function __construct(string $message = 'Forbidden', int $code = 403)
    {
        return parent::__construct($message, $code);
    }
}
