<?php

namespace App\Repositories\Transacation;

use App\Models\Transaction;
use App\DTOs\ReverseDto;
use App\Models\User;

interface TransactionRepositoryInterface
{
    public function createDeposit(Transaction $transaction): Transaction;
    public function createTransfer(Transaction $transaction): Transaction;
    public function reverseTransaction(ReverseDto $reverseDto): Transaction;
    public function findOrFail(int $id): Transaction;
    public function findUserById(int $id): ?User;
    public function createTransaction(array $data): Transaction;
    public function findById(int $id): ?Transaction;
    public function getTransactions($userId);
    public function getRecentTransactions($user);
}
