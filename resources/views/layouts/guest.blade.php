<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:600,700,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">

        <!-- Logo -->
        <div>
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 100px;">
            </a>
        </div>

        <!-- Login Box -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            
            <!-- Nama Aplikasi di dalam kotak login -->
            <h1 style="
                font-size: 28px;
                font-weight: 900;
                color: #fefeff;
                text-align: center;
                margin-bottom: 24px;
            ">
                Branchless Device Management
            </h1>

            {{ $slot }}
        </div>
    </div>
</body>
</html>
