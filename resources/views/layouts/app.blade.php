<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Mi Perfil - BookIt</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="font-sans antialiased bg-slate-100">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @hasSection('header')
            <header class="bg-white shadow-sm border-b border-slate-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-slate-800">
                        @yield('header')
                    </h1>
                </div>
            </header>
        @endif

        @if (session('success'))
            <div class="max-w-7xl mx-auto mx-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>

</html>
