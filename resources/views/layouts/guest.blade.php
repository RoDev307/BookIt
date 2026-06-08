<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="/favicon.ico?v=2" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2">
    <link class="rounded-full" rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2">
    <link rel="manifest" href="/site.webmanifest?v=2">
    <title>Autenticación - BookIt</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- 🌙 Detección e inyección inmediata del tema en el DOM antes de renderizar --}}
    <script>
        if (!localStorage.getItem('theme')) {
            localStorage.setItem('theme', 'light');
        }

        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    {{-- 🚨 BLINDAJE CSS PARA EL ENTORNO DE LOGIN Y REGISTRO (GUEST) --}}
    <style>
        /* MODO DÍA (CLARO) GUEST FORZADO */
        :root[data-theme="light"],
        :root[data-theme="light"] body {
            background-color: #f3f4f6 !important;
            /* bg-slate-100 */
            color: #1e293b !important;
        }

        :root[data-theme="light"] .bg-white {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        :root[data-theme="light"] h2 {
            color: #0f172a !important;
        }

        :root[data-theme="light"] p,
        :root[data-theme="light"] label {
            color: #475569 !important;
        }

        /* MODO NOCHE (OSCURO) GUEST FORZADO PREMIUM */
        :root[data-theme="dark"],
        :root[data-theme="dark"] body {
            background-color: #0f172a !important;
            /* Slate 900 de fondo */
            color: #f1f5f9 !important;
        }

        /* Forzar tarjeta central en Slate 800 */
        :root[data-theme="dark"] .bg-white,
        :root[data-theme="dark"] div[class*="bg-white"],
        :root[data-theme="dark"] div[class*="shadow-md"] {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        /* Títulos y descriptivos en el formulario */
        :root[data-theme="dark"] h2,
        :root[data-theme="dark"] .text-slate-900 {
            color: #ffffff !important;
        }

        :root[data-theme="dark"] p,
        :root[data-theme="dark"] .text-slate-500,
        :root[data-theme="dark"] span {
            color: #94a3b8 !important;
            /* Slate 400 nítido */
        }

        /* Labels e Inputs del Formulario */
        :root[data-theme="dark"] label,
        :root[data-theme="dark"] .text-slate-700 {
            color: #cbd5e1 !important;
            /* Labels legibles */
        }

        :root[data-theme="dark"] input[type="email"],
        :root[data-theme="dark"] input[type="password"],
        :root[data-theme="dark"] input {
            background-color: #0f172a !important;
            /* Inputs oscuros */
            border: 1px solid #334155 !important;
            color: #ffffff !important;
        }

        :root[data-theme="dark"] input::placeholder {
            color: #475569 !important;
        }

        /* Enlaces inferiores */
        :root[data-theme="dark"] .border-t {
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] a.text-slate-400 {
            color: #94a3b8 !important;
        }

        :root[data-theme="dark"] a:hover {
            color: #818cf8 !important;
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased bg-slate-100 dark:bg-slate-900 transition-colors duration-200">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <div class="mb-2">
            <a href="{{ route('businesses.index') }}"
                class="text-3xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight transition-colors">
                BookIt
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white dark:bg-slate-800 shadow-md overflow-hidden sm:rounded-lg border border-slate-200 dark:border-slate-700 transition-colors">
            @yield('content')
        </div>

    </div>
</body>

</html>
