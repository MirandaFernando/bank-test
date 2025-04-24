<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Repositories\Transacation\TransactionRepository;
use App\Repositories\Wallet\WalletRepositoryInterface;
use App\Repositories\Wallet\WalletRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class,
        );

        $this->app->bind(
            WalletRepositoryInterface::class,
            WalletRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
