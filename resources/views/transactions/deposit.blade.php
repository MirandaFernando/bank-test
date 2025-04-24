<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Realizar Depósito') }}
        </h2>
    </x-slot>

    <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-50">
        <div class="w-full sm:max-w-3xl px-6 py-8 mt-6">
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden p-6">
                <div class="p-10 space-y-6">
                    <div class="text-center mb-10">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Depósito Financeiro</h3>
                        <p class="text-gray-600">Insira o valor que deseja depositar em sua conta</p>
                    </div>

                    <form method="POST" action="{{ route('deposit.store') }}">
                        @csrf

                        <div class="mb-8">
                            <x-label for="amount" class="block text-lg font-medium text-gray-700 mb-3" :value="__('Valor (R$)')" />
                            <div class="relative rounded-md shadow-sm">
                                <x-input id="amount" 
                                    class="block w-full pl-12 py-4 text-xl border-gray-300 rounded-lg" 
                                    type="number" 
                                    name="amount" 
                                    placeholder="0,00"
                                    step="0.01" 
                                    min="0.01" 
                                    required 
                                    autofocus />
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 border border-transparent rounded-lg font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                                {{ __('Cancelar') }}
                            </a>

                            <x-button class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-lg">
                                {{ __('Confirmar Depósito') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>