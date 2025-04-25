<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionService;
use App\DTOs\DepositDTO;
use App\DTOs\TransferDto;
use App\DTOs\ReverseDto;

class TransactionController extends Controller
{

    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function showDepositForm()
    {
        return view('transactions.deposit');
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $depositDTO = new DepositDTO($request->amount);
        try {
            $this->transactionService->deposit($depositDTO);

            return redirect()->route('dashboard')
                ->with('success', 'Depósito realizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erro ao realizar o depósito: ' . $e->getMessage());
        }
    }

    public function showTransferForm()
    {
        $users = $this->transactionService->getAvailableUsers(Auth::id());

        return view('transactions.transfer', compact('users'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $transferDto = new TransferDto($request->amount, $request->receiver_id);

        try {
            $this->transactionService->transfer($transferDto);

            return redirect()->route('dashboard')
                ->with('success', 'Transferência realizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erro ao realizar a transferência: ' . $e->getMessage());
        }
    }

    public function index()
    {
        try {
            $transactions = $this->transactionService->getTransactions(Auth::id());

            return view('transactions.index', compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erro ao carregar as transações: ' . $e->getMessage());
        }
    }

    public function reverse(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $reverseDto = new ReverseDto($request->transaction_id);

        try {
            $this->transactionService->reverse($reverseDto);

            return redirect()->route('dashboard')
                ->with('success', 'Transação revertida com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Erro ao reverter a transação: ' . $e->getMessage());
        }
    }
}
