<?php

namespace App\Repositories\Transacation;

use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\DTOs\DepositDTO;
use App\DTOs\TransferDto;
use App\DTOs\ReverseDto;
use App\Models\User;

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

    public function findUserById(int $id): ?User
    {
        return User::find($id);
    }

    public function createTransaction(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function getTransactions($userId)
    {
        return Transaction::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(10);
    }

    public function getRecentTransactions($user)
    {
        return Transaction::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->with(['receiver', 'sender'])
            ->latest()
            ->take(5)
            ->get();
    }

}
