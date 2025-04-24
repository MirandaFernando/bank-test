<?php

namespace App\Providers;

use App\Repositories\Transacation\TransactionRepositoryInterface;
use App\Repositories\Transacation\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
        );
    }
}