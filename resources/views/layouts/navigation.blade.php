<nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

        <div class="flex items-center space-x-8">
            <a href="{{ route('businesses.index') }}" class="text-2xl font-black text-indigo-600 tracking-tight">
                BookIt
            </a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                <a href="{{ route('businesses.index') }}" class="hover:text-indigo-600 transition-colors py-5">
                    ← Volver al Catálogo
                </a>
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600' : 'hover:text-slate-900' }} px-1 py-5 transition-colors">
                    Mis Citas
                </a>
                <a href="{{ route('profile.edit') }}"
                    class="{{ request()->routeIs('profile.edit') ? 'text-indigo-600 font-bold border-b-2 border-indigo-600' : 'hover:text-slate-900' }} px-1 py-5 transition-colors">
                    Mi Perfil
                </a>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-slate-700 hidden sm:inline">
                Cuenta: <strong class="text-indigo-600">{{ Auth::user()->name }}</strong>
            </span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-4 py-2 rounded-xl transition-all shadow-sm cursor-pointer">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <div
        class="md:hidden border-t border-slate-100 bg-slate-50 px-4 py-2 flex justify-around text-xs font-semibold text-slate-600">
        <a href="{{ route('businesses.index') }}" class="hover:text-indigo-600 py-1">Catálogo</a>
        <a href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 font-bold' : '' }} py-1">Mis Citas</a>
        <a href="{{ route('profile.edit') }}"
            class="{{ request()->routeIs('profile.edit') ? 'text-indigo-600 font-bold' : '' }} py-1">Perfil</a>
    </div>
</nav>
