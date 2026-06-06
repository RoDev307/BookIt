<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookIt - Sistema de Citas')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-slate-50 font-sans antialiased">

    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('businesses.index') }}"
                    class="text-2xl font-black text-indigo-600 tracking-tight">BookIt</a>
                <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('businesses.index') }}"
                        class="text-indigo-600 border-b-2 border-indigo-600 px-1 py-5">Inicio</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition-colors">Mis Reservas</a>
                        @if (Auth::user()->role === 'admin_business')
                            <a href="{{ route('services.index') }}"
                                class="text-indigo-500 font-semibold hover:text-indigo-700 transition-colors">Panel
                                Admin</a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="flex items-center space-x-3">
                @auth
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-slate-700 hidden sm:inline">
                            Hola, <strong class="text-indigo-600">{{ Auth::user()->name }}</strong>
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="text-sm font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-xl transition-all cursor-pointer">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-semibold text-slate-700 hover:text-indigo-600 px-3 py-1.5 transition-colors">Iniciar
                        Sesión</a>
                    <a href="{{ route('register') }}"
                        class="text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl transition-colors shadow-sm">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

</body>

</html>
