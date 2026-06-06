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


    <aside id="sidebar" class="w-64 bg-slate-800 text-white flex flex-col justify-between hidden md:fixed md:inset-y-0 md:left-0 md:flex z-50">
        <div class="p-5">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-400 mb-8">BookIt Admin</h1>
            <nav class="space-y-2">
                <a href="#" class="block py-2.5 px-4 rounded transition bg-slate-900 text-white font-medium">
                     Dashboard
                </a>
                <a href="#" class="block py-2.5 px-4 rounded transition text-slate-300 hover:bg-slate-700 hover:text-white">
                     Gestionar Citas
                </a>
                <a href="#" class="block py-2.5 px-4 rounded transition text-slate-300 hover:bg-slate-700 hover:text-white">
                     Horarios de Atención
                </a>
            </nav>
        </div>
        <div class="p-5 border-t border-slate-700 text-sm text-slate-400">
            Conectado como Administrador
        </div>
    </aside>

    <!-- Se agregó md:ml-64 para que el contenido no se monte bajo la barra lateral fija en computadoras -->
    <div class="flex-1 flex flex-col overflow-y-auto md:ml-64">
        <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
            <button id="menu-btn" class="md:hidden text-gray-600 focus:outline-none text-xl cursor-pointer">☰</button>//esto aparece solo cuando la pantalla se hace mas pequeña
            <h2 class="text-xl font-semibold text-gray-700">Panel de Control</h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 font-medium">Admin Global</span>
<a href="/" class="text-sm bg-indigo-600 text-white font-medium px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">//el botton para regresar a inicio
    Ver Sitio Público →
</a>            </div>
        </header>

        <main class="p-6 flex-1">
            @yield('content')
        </main>
    </div>

    <!-- RECOMENDACIÓN: El script es mejor tenerlo al puro final antes del cierre del body -->
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            // Agregamos flex para que cuando no esté 'hidden' en móvil, se ordene correctamente
            sidebar.classList.toggle('flex');
        });
    </script>
</body>
</html>
