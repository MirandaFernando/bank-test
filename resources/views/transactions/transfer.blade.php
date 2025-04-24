<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Realizar Transferência') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('transfer.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-label for="receiver_id" :value="__('Destinatário')" />
                            <select id="receiver_id" name="receiver_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-label for="amount" :value="__('Valor')" />
                            <x-input id="amount" class="block mt-1 w-full" 
                                type="number" 
                                name="amount" 
                                step="0.01" 
                                min="0.01" 
                                required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('dashboard') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Cancelar') }}
                            </a>

                            <x-button class="ml-4">
                                {{ __('Transferir') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>