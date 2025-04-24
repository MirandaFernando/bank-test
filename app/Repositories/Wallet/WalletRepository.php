<?php

namespace App\Repositories\Wallet;

use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Models\Wallet;
use App\Models\User;

class WalletRepository implements WalletRepositoryInterface
{
    public function incrementBalance(Wallet $wallet, float $amount): void
    {
        $wallet->increment('balance', $amount);
    }

    public function decrementBalance(Wallet $wallet, float $amount): void
    {
        $wallet->decrement('balance', $amount);
    }

    public function getWallet(User $user): Wallet
    {
        return $user->wallet;
    }
}