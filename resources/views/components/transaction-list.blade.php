<!-- resources/views/components/transaction-list.blade.php -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Últimas Transações</h3>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($transactions as $transaction)
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex justify-between">
                    <div>
                        <p class="font-medium">
                            @if($transaction->type === 'deposit')
                                Depósito
                            @else
                                Transferência {{ $transaction->sender_id === auth()->id() ? 'Enviada' : 'Recebida' }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium {{ $transaction->receiver_id === auth()->id() ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->receiver_id === auth()->id() ? '+' : '-' }} 
                            R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500">
                            @if($transaction->type === 'transfer')
                                {{ $transaction->sender_id === auth()->id() ? 'Para: ' . $transaction->receiver->name : 'De: ' . $transaction->sender->name }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $transaction->status === 'reversed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $transaction->status === 'completed' ? 'Concluído' : '' }}
                        {{ $transaction->status === 'pending' ? 'Pendente' : '' }}
                        {{ $transaction->status === 'reversed' ? 'Estornado' : '' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                Nenhuma transação encontrada
            </div>
        @endforelse
    </div>
    @if(isset($recentTransactions) && count($recentTransactions) > 0)
        <div class="p-4 border-t border-gray-200 text-center">
            <a href="{{ route('transactions.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                Ver todas as transações
            </a>
        </div>
    @endif
</div>

<script>
    console.log(@json($transactions));
</script>