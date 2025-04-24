<?php

namespace App\Repositories\Transacation;

use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\DTOs\DepositDTO;
use App\DTOs\TransferDto;
use App\DTOs\ReverseDto;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function createDeposit(Transaction $transaction): Transaction
    {
        return Transaction::create([
            'amount' => $transaction->getAmount(),
            'type' => Transaction::TYPE_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => $transaction->getUserId(),
            'receiver_id' => $transaction->getUserId(),
            'wallet_id' => $transaction->getWalletId(),
        ]);
    }

    public function createTransfer(Transaction $transaction): Transaction
    {
        return Transaction::create([
            'amount' => $transaction->getAmount(),
            'type' => Transaction::TYPE_TRANSFER,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => $transaction->getSenderId(),
            'receiver_id' => $transaction->getReceiverId(),
            'wallet_id' => $transaction->getWalletId(),
        ]);
    }

    public function reverseTransaction(ReverseDto $reverseDto): Transaction
    {
        $transaction = $this->findOrFail($reverseDto->getTransactionId());
        
        $transaction->update([
            'status' => Transaction::STATUS_REVERSED,
            'reversed_at' => now()
        ]);

        return $transaction->fresh();
    }

    public function findOrFail(int $id): Transaction
    {
        return Transaction::findOrFail($id);
    }
}