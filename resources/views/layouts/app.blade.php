<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <title>Mi Perfil - BookIt</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- 🌙 Script de detección anti-parpadeo --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body
    class="font-sans antialiased bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @hasSection('header')
            <header
                class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700 transition-colors">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                        @yield('header')
                    </h1>
                </div>
            </header>
        @endif

        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div
                    class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-xl shadow-sm">
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
