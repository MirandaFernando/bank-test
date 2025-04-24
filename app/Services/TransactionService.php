<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\DTOs\DepositDTO;
use App\DTOs\TransferDto;
use Exception;
use App\DTOs\ReverseDto;
use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Repositories\Wallet\WalletRepositoryInterface;

class TransactionService
{

    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private WalletRepositoryInterface $walletRepository
    ) {}

    public function deposit(DepositDTO $depositDTO): void
    {
        $user = Auth::user();
        $amount = $depositDTO->getAmount();

        DB::beginTransaction();

        try {
            $transaction = new Transaction([
                'amount' => $amount,
                'type' => Transaction::TYPE_DEPOSIT,
                'status' => Transaction::STATUS_PENDING,
                'sender_id' => $user->id,
                'receiver_id' => $user->id,
                'wallet_id' => $user->wallet->id,
            ]);

            $this->transactionRepository->createDeposit($transaction);

            $user->wallet->increment('balance', $amount);

            $transaction->update(['status' => Transaction::STATUS_COMPLETED]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function transfer(TransferDto $transferDto)
    {
        $sender = Auth::user();
        $receiver = User::find($transferDto->getReceiverId());
        $amount = $transferDto->getAmount();

        DB::beginTransaction();

        try {
            if ($sender->wallet->balance < $amount) {
                throw new Exception('Saldo insuficiente para a transferência.');
            }

            Transaction::create([
                'amount' => $amount,
                'type' => Transaction::TYPE_TRANSFER,
                'status' => Transaction::STATUS_COMPLETED,
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'wallet_id' => $sender->wallet->id,
            ]);

            $sender->wallet->decrement('balance', $amount);
            $receiver->wallet->increment('balance', $amount);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function reverse(ReverseDto $reverseDto): void
    {

        $transaction = Transaction::findOrFail($reverseDto->getTransactionId());

        if ($transaction->sender_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para reverter esta transação');
        }

        DB::beginTransaction();

        try {
            if ($transaction->type === Transaction::TYPE_TRANSFER) {
                $transaction->sender->wallet->increment('balance', $transaction->amount);
                $transaction->receiver->wallet->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === Transaction::TYPE_DEPOSIT) {
                $transaction->receiver->wallet->decrement('balance', $transaction->amount);
            }

            $transaction->update([
                'status' => Transaction::STATUS_REVERSED,
                'reversed_at' => now()
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAvailableUsers($userId)
    {
        return User::where('id', '!=', $userId)->get();
    }

    public function getTransactions($userId)
    {
        return Transaction::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(10);
    }

    public function getRecentTransactions($user){

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
