<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedTransactionException extends Exception
{
    protected $message = 'Você não tem permissão para realizar esta operação.';

    public function __construct($message = null, $code = 403, Exception $previous = null)
    {
        parent::__construct($message ?? $this->message, $code, $previous);
    }
}
