<?php

namespace App\Exceptions;

use Exception;

class AlreadyReversedException extends Exception
{
    protected $message = 'Esta transação já foi revertida anteriormente.';

    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}
