<?php

namespace App\DTOs;

class ReverseDto
{
    private string $transaction_id;

    public function __construct(string $transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    public function getTransactionId(): string
    {
        return $this->transaction_id;
    }

    public function setTransactionId(string $transaction_id): void
    {
        $this->transaction_id = $transaction_id;
    }
}