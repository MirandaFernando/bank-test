<?php

namespace App\Repositories\Transacation;

use App\Models\Transaction;
use App\DTOs\DepositDTO;
use App\DTOs\TransferDto;
use App\DTOs\ReverseDto;

interface TransactionRepositoryInterface
{
    public function createDeposit(Transaction $transaction): Transaction;
    public function createTransfer(Transaction $transaction): Transaction;
    public function reverseTransaction(ReverseDto $reverseDto): Transaction;
    public function findOrFail(int $id): Transaction;
}