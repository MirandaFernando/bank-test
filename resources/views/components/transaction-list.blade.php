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
                <div class="mt-2 flex justify-between items-center">
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $transaction->status === 'reversed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $transaction->status === 'completed' ? 'Concluído' : '' }}
                        {{ $transaction->status === 'pending' ? 'Pendente' : '' }}
                        {{ $transaction->status === 'reversed' ? 'Estornado' : '' }}
                    </span>
                    
                    @if($transaction->status === 'completed' && $transaction->sender_id === auth()->id())
                        <div x-data="{ open: false }" x-init="open = false">
                            <button @click="open = true" 
                                    class="text-xs px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                Reverter
                            </button>
                            <template x-teleport="body">
                                <div x-show="open" 
                                     x-transition.opacity
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl"
                                         @click.away="open = false">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar reversão</h3>
                                        <p class="text-gray-600 mb-6">Tem certeza que deseja reverter esta transação?</p>
                                        <div class="flex justify-end space-x-3">
                                            <button @click="open = false" 
                                                    type="button" 
                                                    class="px-4 py-2 text-gray-700 hover:text-gray-900 rounded border border-gray-300">
                                                Cancelar
                                            </button>
                                            <form method="POST" action="{{ route('transactions.reverse') }}">
                                                @csrf
                                                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    Confirmar Reversão
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @endif
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
