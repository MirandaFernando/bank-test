<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionService;

class DashboardController extends Controller
{

    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function index()
    {
        $user = \Auth::user();

        $balance = $user->wallet->balance;
        $recentTransactions = $this->transactionService->getRecentTransactions($user);

        return view('dashboard', [
            'balance' => $balance,
            'transactions' => $recentTransactions,
        ]);
    }
}
