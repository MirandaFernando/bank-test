<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\InvalidTransactionStatusException;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function showDepositForm()
    {;
        return view('transactions.deposit');
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        Transaction::create([
            'amount' => $amount,
            'type' => Transaction::TYPE_DEPOSIT,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => $user->id,
            'receiver_id' => $user->id,
            'wallet_id' => $user->wallet->id,
        ]);

        $user->wallet->increment('balance', $amount);

        return redirect()->route('dashboard')
            ->with('success', 'Depósito realizado com sucesso!');
    }

    // Mostrar formulário de transferência
    public function showTransferForm()
    {
        return view('transactions.transfer', [
            'users' => User::where('id', '!=', Auth::id())->get()
        ]);
    }

    // Processar transferência
    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $sender = Auth::user();
        $receiver = User::find($request->receiver_id);
        $amount = $request->amount;

        // Verificar saldo suficiente
        if ($sender->wallet->balance < $amount) {
            return back()->with('error', 'Saldo insuficiente para a transferência.');
        }

        Transaction::create([
            'amount' => $amount,
            'type' => Transaction::TYPE_TRANSFER,
            'status' => Transaction::STATUS_COMPLETED,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'wallet_id' => $sender->wallet->id,
        ]);

        // Atualizar saldos
        $sender->wallet->decrement('balance', $amount);
        $receiver->wallet->increment('balance', $amount);

        return redirect()->route('dashboard')
            ->with('success', 'Transferência realizada com sucesso!');
    }

    // Listar todas as transações
    public function index()
    {
        $transactions = Transaction::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function reverse(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        
        // Verifica se o usuário tem permissão para reverter
        if ($transaction->sender_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para reverter esta transação');
        }

        if ($transaction->status !== Transaction::STATUS_COMPLETED) {
            throw new InvalidTransactionStatusException('Apenas transações concluídas podem ser revertidas');
        }

        DB::transaction(function () use ($transaction) {
            if ($transaction->type === Transaction::TYPE_TRANSFER) {
                // Reverte transferência
                $transaction->sender->wallet->increment('balance', $transaction->amount);
                $transaction->receiver->wallet->decrement('balance', $transaction->amount);
            } elseif ($transaction->type === Transaction::TYPE_DEPOSIT) {
                // Reverte depósito
                $transaction->receiver->wallet->decrement('balance', $transaction->amount);
            }

            $transaction->update([
                'status' => Transaction::STATUS_REVERSED,
                'reversed_at' => now()
            ]);
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transação revertida com sucesso!');
    }
}