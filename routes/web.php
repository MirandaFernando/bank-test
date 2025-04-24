<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Outras rotas autenticadas
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Rotas de transações
    Route::get('/deposito', [TransactionController::class, 'showDepositForm'])->name('deposit');
    Route::post('/deposito', [TransactionController::class, 'deposit'])->name('deposit.store');
    
    Route::get('/transferencia', [TransactionController::class, 'showTransferForm'])->name('transfer');
    Route::post('/transferencia', [TransactionController::class, 'transfer'])->name('transfer.store');
    
    Route::get('/transacoes', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/reverter-transacao', [TransactionController::class, 'reverse'])->name('transactions.reverse');
});

require __DIR__.'/auth.php';
