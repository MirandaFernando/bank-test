<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Example: Fetch balance from the authenticated user or set a default value
        $balance = auth()->user()->balance ?? 0.00;

        return view('dashboard', [
            'balance' => $balance,
        ]);
    }
}