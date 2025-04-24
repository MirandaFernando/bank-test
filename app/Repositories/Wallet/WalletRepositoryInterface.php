<?php

namespace App\Repositories\Wallet;

use App\Models\User;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function incrementBalance(Wallet $wallet, float $amount): void;
    public function decrementBalance(Wallet $wallet, float $amount): void;
    public function getWallet(User $user): Wallet;
}