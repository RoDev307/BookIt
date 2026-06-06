<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel de Administración - BookIt</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <aside id="sidebar"
        class="w-64 bg-slate-800 text-white flex flex-col justify-between hidden md:fixed md:inset-y-0 md:left-0 md:flex z-50">
        <div class="p-5">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-400 mb-8">BookIt Admin</h1>
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="block py-2.5 px-4 rounded transition {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('services.index') }}"
                    class="block py-2.5 px-4 rounded transition {{ request()->routeIs('services.*') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    🛠️ Gestionar Servicios
                </a>
                <a href="{{ route('appointments.mis-citas') }}"
                    class="block py-2.5 px-4 rounded transition {{ request()->routeIs('appointments.*') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    📅 Gestionar Citas
                </a>
            </nav>
        </div>
        <div class="p-5 border-t border-slate-700 text-sm text-slate-400 flex justify-between items-center">
            <span>👤 {{ Auth::user()->name ?? 'Admin Global' }}</span>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-y-auto md:ml-64">

        <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <button id="menu-btn"
                class="md:hidden text-gray-600 focus:outline-none text-xl cursor-pointer p-1 rounded hover:bg-gray-100">
                ☰
            </button>

            <h2 class="text-xl font-semibold text-gray-700">Consola de Control</h2>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 font-medium hidden sm:inline">Administrador</span>
                <a href="{{ route('businesses.index') }}"
                    class="text-sm bg-indigo-600 text-white font-medium px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors shadow-sm">
                    Ver Sitio Público →
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="mx-6 mt-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-6 mt-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <main class="p-6 flex-1">
            @yield('content')
        </main>
    </div>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');

        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
        });

        // Cerrar menú si se da clic fuera de él en dispositivos móviles
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && !sidebar.classList.contains('hidden') && !sidebar.contains(e.target) && e
                .target !== menuBtn) {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
            }
        });
    </script>
</body>

</html>
