<?php

namespace App\Exceptions;

use Exception;

class TransactionReversalException extends Exception
{
    protected $message = 'Não foi possível reverter a transação.';

    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}
