<?php

namespace Tests\Unit\Services;

use App\DTOs\DepositDTO;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    public function testDepositSuccess()
    {
        $depositDTO = $this->createMock(DepositDTO::class);
        $depositDTO->method('getAmount')->willReturn(100.00);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->expects($this->once())
            ->method('createDeposit')
            ->with($this->callback(function ($transaction) {
                $expected = [
                    'amount' => 100.0,
                    'type' => 'deposit',
                    'status' => 'pending',
                    'sender_id' => 1,
                    'receiver_id' => 1,
                    'wallet_id' => 1,
                ];

                return $transaction instanceof Transaction &&
                    $transaction->getAttributes() === $expected;
            }));

        $wallet = new class extends Wallet {
            public function increment($column, $amount = 1, array $extra = [])
            {
                return true;
            }
        };
        $wallet->id = 1;

        $user = new User();
        $user->id = 1;
        $user->setRelation('wallet', $wallet);

        Auth::shouldReceive('user')->andReturn($user);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);
        $service = new TransactionService($transactionRepository, $walletRepository);

        $service->deposit($depositDTO);
    }
}
