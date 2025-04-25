<?php

namespace Tests\Unit\Services;

use App\DTOs\DepositDTO;
use App\DTOs\ReverseDto;
use App\Exceptions\AlreadyReversedException;
use App\Exceptions\InsufficientFundsException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Tests\TestCase;
use Mockery;

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

    public function testTransferSuccess()
    {
        $transferDTO = $this->createMock(\App\DTOs\TransferDto::class);
        $transferDTO->method('getReceiverId')->willReturn(2);
        $transferDTO->method('getAmount')->willReturn(50.00);

        $senderWallet = new class extends Wallet {
            public $balance = 100;
            public function decrement($column, $amount = 1, array $extra = []) { return true; }
        };

        $receiverWallet = new class extends Wallet {
            public function increment($column, $amount = 1, array $extra = []) { return true; }
        };

        $sender = new User();
        $sender->id = 1;
        $sender->setRelation('wallet', $senderWallet);

        $receiver = new User();
        $receiver->id = 2;
        $receiver->setRelation('wallet', $receiverWallet);

        Auth::shouldReceive('user')->andReturn($sender);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->method('findUserById')->with(2)->willReturn($receiver);
        $transactionRepository->method('createTransaction')->willReturn(new Transaction());

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->transfer($transferDTO);
    }

    public function testTransferThrowsInsufficientFundsException()
    {
        $this->expectException(InsufficientFundsException::class);

        $transferDTO = $this->createMock(\App\DTOs\TransferDto::class);
        $transferDTO->method('getReceiverId')->willReturn(2);
        $transferDTO->method('getAmount')->willReturn(150.00);

        $senderWallet = new Wallet();
        $senderWallet->balance = 100;

        $sender = new User();
        $sender->id = 1;
        $sender->setRelation('wallet', $senderWallet);

        Auth::shouldReceive('user')->andReturn($sender);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->transfer($transferDTO);
    }


    public function testGetTransactions()
    {
        $userId = 1;
        $expectedTransactions = collect([
            new Transaction(['id' => 1, 'amount' => 100]),
            new Transaction(['id' => 2, 'amount' => 200]),
        ]);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->expects($this->once())
            ->method('getTransactions')
            ->with($userId)
            ->willReturn($expectedTransactions);

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $result = $service->getTransactions($userId);

        $this->assertEquals($expectedTransactions, $result);
    }

    public function testGetRecentTransactions()
    {
        $user = new User(['id' => 1]);
        $expectedTransactions = collect([
            new Transaction(['id' => 1, 'amount' => 100]),
            new Transaction(['id' => 2, 'amount' => 200]),
        ]);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->expects($this->once())
            ->method('getRecentTransactions')
            ->with($user)
            ->willReturn($expectedTransactions);

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $result = $service->getRecentTransactions($user);

        $this->assertEquals($expectedTransactions, $result);
    }

    public function testReverseTransferSuccess()
    {
        $reverseDto = $this->createMock(ReverseDto::class);
        $reverseDto->method('getTransactionId')->willReturn('1');

        $transaction = new Transaction([
            'id' => 1,
            'amount' => 50.00,
            'type' => Transaction::TYPE_TRANSFER,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => 1,
            'receiver_id' => 2,
            'wallet_id' => 1,
        ]);

        $senderWallet = new class extends Wallet {
            public $balance = 50;
            public function increment($column, $amount = 1, array $extra = []) { return true; }
        };

        $receiverWallet = new class extends Wallet {
            public $balance = 150;
            public function decrement($column, $amount = 1, array $extra = []) { return true; }
        };

        $sender = new User();
        $sender->id = 1;
        $sender->setRelation('wallet', $senderWallet);

        $receiver = new User();
        $receiver->id = 2;
        $receiver->setRelation('wallet', $receiverWallet);

        $transaction->setRelation('sender', $sender);
        $transaction->setRelation('receiver', $receiver);

        Auth::shouldReceive('user')->andReturn($sender);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->method('findById')->with(1)->willReturn($transaction);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->reverse($reverseDto);
    }

    public function testReverseDepositSuccess()
    {
        $reverseDto = $this->createMock(ReverseDto::class);
        $reverseDto->method('getTransactionId')->willReturn('1');

        $transaction = new Transaction([
            'id' => 1,
            'amount' => 100.00,
            'type' => Transaction::TYPE_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => 1,
            'receiver_id' => 1,
            'wallet_id' => 1,
        ]);

        $wallet = new class extends Wallet {
            public $balance = 200;
            public function decrement($column, $amount = 1, array $extra = []) { return true; }
        };

        $user = new User();
        $user->id = 1;
        $user->setRelation('wallet', $wallet);

        $transaction->setRelation('receiver', $user);

        Auth::shouldReceive('user')->andReturn($user);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->method('findById')->with(1)->willReturn($transaction);

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->reverse($reverseDto);
    }

    public function testReverseThrowsAlreadyReversedException()
    {
        $this->expectException(AlreadyReversedException::class);

        $reverseDto = $this->createMock(ReverseDto::class);
        $reverseDto->method('getTransactionId')->willReturn('1');

        $transaction = new Transaction([
            'id' => 1,
            'amount' => 100.00,
            'type' => Transaction::TYPE_DEPOSIT,
            'status' => Transaction::STATUS_REVERSED,
            'sender_id' => 1,
            'receiver_id' => 1,
            'wallet_id' => 1,
        ]);

        $user = new User();
        $user->id = 1;

        Auth::shouldReceive('user')->andReturn($user);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->method('findById')->with(1)->willReturn($transaction);

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->reverse($reverseDto);
    }

    public function testReverseThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $reverseDto = $this->createMock(ReverseDto::class);
        $reverseDto->method('getTransactionId')->willReturn('1');

        $transaction = new Transaction([
            'id' => 1,
            'amount' => 100.00,
            'type' => Transaction::TYPE_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => 2,
            'receiver_id' => 2,
            'wallet_id' => 2,
        ]);

        $user = new User();
        $user->id = 1;

        Auth::shouldReceive('user')->andReturn($user);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->method('findById')->with(1)->willReturn($transaction);

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->reverse($reverseDto);
    }

    public function testReverseThrowsInsufficientFundsExceptionForDeposit()
    {
        $this->expectException(InsufficientFundsException::class);

        $reverseDto = $this->createMock(ReverseDto::class);
        $reverseDto->method('getTransactionId')->willReturn('1');

        $transaction = new Transaction([
            'id' => 1,
            'amount' => 100.00,
            'type' => Transaction::TYPE_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => 1,
            'receiver_id' => 1,
            'wallet_id' => 1,
        ]);

        $wallet = new class extends Wallet {
            public $balance = 50;
        };

        $user = new User();
        $user->id = 1;
        $user->setRelation('wallet', $wallet);

        $transaction->setRelation('receiver', $user);

        Auth::shouldReceive('user')->andReturn($user);

        $transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $transactionRepository->method('findById')->with(1)->willReturn($transaction);

        $walletRepository = $this->createMock(WalletRepositoryInterface::class);

        $service = new TransactionService($transactionRepository, $walletRepository);
        $service->reverse($reverseDto);
    }
}
