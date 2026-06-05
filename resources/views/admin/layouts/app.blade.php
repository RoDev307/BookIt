<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin panel</title>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-800 text-white flex flex-col justify-between hidden md:flex">
        <div class="p-5">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-400 mb-8">BookIt Admin</h1>
            <nav class="space-y-2">
                <a href="#" class="block py-2.5 px-4 rounded transition bg-slate-900 text-white font-medium">
                    📊 Dashboard
                </a>
                <a href="#" class="block py-2.5 px-4 rounded transition text-slate-300 hover:bg-slate-700 hover:text-white">
                    📅 Gestionar Citas
                </a>
                <a href="#" class="block py-2.5 px-4 rounded transition text-slate-300 hover:bg-slate-700 hover:text-white">
                    🕒 Horarios de Atención
                </a>
            </nav>
        </div>
        <div class="p-5 border-t border-slate-700 text-sm text-slate-400">
            Conectado como Administrador
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
            <button class="md:hidden text-gray-600 focus:outline-none">☰</button>
            <h2 class="text-xl font-semibold text-gray-700">Panel de Control</h2>
            <div class="flex items-center space-y-4">
                <span class="text-sm text-gray-600 mr-2 font-medium">Admin Global</span>
                <a href="/" class="text-sm text-indigo-600 hover:underline">Ver Sitio Público →</a>
            </div>
        </header>

        <main class="p-6 flex-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
