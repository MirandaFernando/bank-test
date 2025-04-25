<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .fade-out {
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
    </style>
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <!-- Flash Messages -->
    @if(session('success'))
        <div id="flash-success" class="fixed inset-x-0 top-4 z-50 flex justify-center">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg max-w-md w-full mx-4 flex justify-between items-center">
                <div>
                    <p class="font-bold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
                <button onclick="document.getElementById('flash-success').remove()" type="button" class="text-green-700 hover:text-green-900">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="flash-error" class="fixed inset-x-0 top-4 z-50 flex justify-center">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg max-w-md w-full mx-4 flex justify-between items-center">
                <div>
                    <p class="font-bold">Erro!</p>
                    <p>{{ session('error') }}</p>
                </div>
                <button onclick="document.getElementById('flash-error').remove()" type="button" class="text-red-700 hover:text-red-900">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
        </div>
    @endif

    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>
</div>

<!-- Script para esconder automaticamente os alertas -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const success = document.getElementById('flash-success');
        const error = document.getElementById('flash-error');

        [success, error].forEach(el => {
            if (el) {
                setTimeout(() => {
                    el.classList.add('fade-out');
                    setTimeout(() => el.remove(), 500);
                }, 5000);
            }
        });
    });
</script>
</body>
</html>
