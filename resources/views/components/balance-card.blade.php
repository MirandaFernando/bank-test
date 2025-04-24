<!-- resources/views/components/balance-card.blade.php -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-gray-500">Saldo Disponível</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">
                R$ {{ number_format($balance, 2, ',', '.') }}
            </p>
        </div>
        <div class="bg-green-100 p-3 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="mt-4 flex space-x-3">
        <a href="{{ route('deposit') }}" class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-md text-center font-medium hover:bg-indigo-700">
            Depositar
        </a>
        <a href="{{ route('transfer') }}" class="flex-1 border border-indigo-600 text-indigo-600 py-2 px-4 rounded-md text-center font-medium hover:bg-indigo-50">
            Transferir
        </a>
    </div>
    </div>
</div>