<?php

namespace App\Exceptions;

class NotImplementedException extends \LogicException
{
    public function __construct(string $message = 'NOT IMPLEMENTED', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
