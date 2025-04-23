<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = \Auth::user(); // Ensure correct usage of the auth helper

        $balance = $user->wallet->balance;
        $recentTransactions = Transaction::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })
        ->with(['receiver', 'sender'])
        ->latest()
        ->take(5)
        ->get();

        return view('dashboard', [
            'balance' => $balance,
            'transactions' => $recentTransactions,
        ]);
    }
}