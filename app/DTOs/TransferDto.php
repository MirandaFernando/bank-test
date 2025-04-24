<?php

namespace App\DTOs;

class TransferDto
{
    private float $amount;
    private int $receiver_id;

    public function __construct(float $amount, int $receiver_id)
    {
        $this->amount = $amount;
        $this->receiver_id = $receiver_id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getReceiverId(): int
    {
        return $this->receiver_id;
    }

    public function setReceiverId(int $receiver_id): void
    {
        $this->receiver_id = $receiver_id;
    }
}