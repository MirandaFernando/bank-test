<?php

namespace App\Exceptions;

use Exception;


class InvalidTransactionStatusException extends Exception
{
    /**
     * InvalidTransactionStatusException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "Invalid transaction status.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}