<nav
    class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700 sticky top-0 z-50 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

        <div class="flex items-center space-x-8">
            <a href="{{ route('businesses.index') }}"
                class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                BookIt
            </a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600 dark:text-slate-300">
                <a href="{{ route('businesses.index') }}"
                    class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors py-5">
                    &larr; Volver al Catálogo
                </a>
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400 font-bold border-b-2 border-indigo-600 dark:border-indigo-400' : 'hover:text-slate-900 dark:hover:text-white' }} px-1 py-5 transition-colors">
                    Mis Citas
                </a>
                <a href="{{ route('profile.edit') }}"
                    class="{{ request()->routeIs('profile.edit') ? 'text-indigo-600 dark:text-indigo-400 font-bold border-b-2 border-indigo-600 dark:border-indigo-400' : 'hover:text-slate-900 dark:hover:text-white' }} px-1 py-5 transition-colors">
                    Mi Perfil
                </a>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300 hidden sm:inline">
                Cuenta: <strong class="text-indigo-600 dark:text-indigo-400">{{ Auth::user()->name }}</strong>
            </span>

            <button id="nav-theme-toggle"
                class="p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all cursor-pointer border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-sm font-bold">
                <span id="nav-theme-toggle-icon">🌙</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-300 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 px-4 py-2 rounded-xl transition-all shadow-sm cursor-pointer">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div
        class="md:hidden border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2 flex justify-around text-xs font-semibold text-slate-600 dark:text-slate-300">
        <a href="{{ route('businesses.index') }}" class="hover:text-indigo-600 py-1">Catálogo</a>
        <a href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400 font-bold' : '' }} py-1">Mis
            Citas</a>
        <a href="{{ route('profile.edit') }}"
            class="{{ request()->routeIs('profile.edit') ? 'text-indigo-600 dark:text-indigo-400 font-bold' : '' }} py-1">Perfil</a>
    </div>
</nav>

<script>
    const navToggleBtn = document.getElementById('nav-theme-toggle');
    const navToggleIcon = document.getElementById('nav-theme-toggle-icon');

    function updateNavToggleUI() {
        if (document.documentElement.classList.contains('dark')) {
            navToggleIcon.innerText = '☀️';
        } else {
            navToggleIcon.innerText = '🌙';
        }
    }

    updateNavToggleUI();

    navToggleBtn.addEventListener('click', function() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
        updateNavToggleUI();
    });
</script>
