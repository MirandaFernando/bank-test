<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DTOs\DepositDTO;
use App\DTOs\TransferDto;
use Exception;
use App\DTOs\ReverseDto;
use App\Exceptions\AlreadyReversedException;
use App\Exceptions\InsufficientFundsException;
use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Repositories\Wallet\WalletRepositoryInterface;
use Illuminate\Validation\UnauthorizedException;

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
            Log::channel('transactions')->error('Falha ao realizar depósito', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'operation' => 'deposit'
            ]);

            DB::rollBack();
            throw $e;
        }
    }

    public function transfer(TransferDto $transferDto)
    {
        $sender = Auth::user();
        $receiver = $this->transactionRepository->findUserById($transferDto->getReceiverId());
        $amount = $transferDto->getAmount();

        DB::beginTransaction();

        try {
            if ($sender->wallet->balance < $amount) {
                Log::channel('transactions')->warning('Saldo insuficiente para transferência', [
                    'sender_id' => $sender->id,
                    'current_balance' => $sender->wallet->balance,
                    'attempted_amount' => $amount,
                    'operation' => 'transfer'
                ]);
                throw new InsufficientFundsException();
            }

            $transaction = $this->transactionRepository->createTransaction([
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
            Log::channel('transactions')->error('Falha ao realizar transferência', [
                'sender_id' => $sender->id,
                'receiver_id' => $receiver?->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'operation' => 'transfer'
            ]);

            DB::rollBack();
            throw $e;
        }
    }

    public function reverse(ReverseDto $reverseDto): void
    {
        $transaction = $this->transactionRepository->findById($reverseDto->getTransactionId());
        $user = Auth::user();

        if ($transaction->sender_id !== $user->id) {
            throw new UnauthorizedException();
        }

        if ($transaction->status === Transaction::STATUS_REVERSED) {
            throw new AlreadyReversedException();
        }

        DB::beginTransaction();

        try {
            $this->validateReverseTransaction($transaction);

            if ($transaction->type === Transaction::TYPE_TRANSFER) {
                $this->reverseTransfer($transaction);
            } elseif ($transaction->type === Transaction::TYPE_DEPOSIT) {
                $this->reverseDeposit($transaction);
            }

            $transaction->update([
                'status' => Transaction::STATUS_REVERSED,
                'reversed_at' => now()
            ]);

            DB::commit();
        } catch (Exception $e) {
            Log::channel('transactions')->error('Falha ao reverter transação', [
                'transaction_id' => $transaction->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'operation' => 'reverse'
            ]);

            DB::rollBack();
            throw $e;
        }
    }

    private function validateReverseTransaction(Transaction $transaction): void
    {
        if ($transaction->type === Transaction::TYPE_DEPOSIT &&
            $transaction->receiver->wallet->balance < $transaction->amount) {
            throw new InsufficientFundsException();
        }
    }

    private function reverseTransfer(Transaction $transaction): void
    {
        $transaction->sender->wallet->increment('balance', $transaction->amount);
        $transaction->receiver->wallet->decrement('balance', $transaction->amount);

    }

    private function reverseDeposit(Transaction $transaction): void
    {
        $oldBalance = $transaction->receiver->wallet->balance;
        $transaction->receiver->wallet->decrement('balance', $transaction->amount);
    }

    public function getAvailableUsers($userId)
    {
        return User::where('id', '!=', $userId)->get();
    }

    public function getTransactions($userId)
    {
        return $this->transactionRepository->getTransactions($userId);
    }

    public function getRecentTransactions($user)
    {
        return $this->transactionRepository->getRecentTransactions($user);
    }
}
