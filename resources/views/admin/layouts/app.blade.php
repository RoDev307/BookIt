<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel de Administración - BookIt</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <aside id="sidebar"
        class="w-64 bg-slate-800 text-white flex flex-col justify-between hidden md:fixed md:inset-y-0 md:left-0 md:flex z-50">
        <div class="p-5">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-400 mb-8">BookIt Admin</h1>
            <nav class="space-y-2">
                <a href="{{ route('services.index') }}"
                    class="block py-2.5 px-4 rounded transition {{ request()->routeIs('services.*') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    🛠️ Gestionar Servicios
                </a>
                <a href="{{ route('admin.appointments.create') }}"
                    class="block py-2.5 px-4 rounded transition {{ request()->routeIs('admin.appointments.create') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    ⚡ Agendar Cita (Interno)
                </a>
                <a href="{{ route('dashboard') }}"
                    class="block py-2.5 px-4 rounded transition {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    📅 Gestionar Citas
                </a>

                {{-- BLOQUE EXCLUSIVO: Acceso al Panel Maestro solo para el Super Administrador --}}
                @if (Auth::user()->email === 'admin@bookit.com')
                    <div class="mt-6 pt-4 border-t border-slate-700">
                        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">SaaS Global
                        </p>
                        <a href="{{ route('master.businesses.index') }}"
                            class="flex items-center justify-between py-2.5 px-4 rounded transition font-bold {{ request()->routeIs('master.businesses.*') ? 'bg-indigo-900 text-white' : 'text-indigo-300 hover:bg-slate-700 hover:text-indigo-200' }}">
                            <span>🌐 Controlar Comercios</span>
                            <span
                                class="text-[9px] bg-indigo-500 text-white px-1.5 py-0.5 rounded font-black uppercase tracking-wider">
                                Root
                            </span>
                        </a>
                    </div>
                @endif
            </nav>
        </div>

        <div class="p-5 border-t border-slate-700 text-sm text-slate-400 flex flex-col gap-3">
            <div class="flex items-center gap-2">
                <span>👤 {{ Auth::user()->name ?? 'Admin Global' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left font-semibold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                    🚪 Cerrar Sesión
                </button>
            </form>
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
                @if (is_null(Auth::user()->business_id))
                    <a href="{{ route('businesses.index') }}"
                        class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-xl transition-colors shadow-sm">
                        Ver Sitio Público →
                    </a>
                @endif
            </div>
        </header>

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
        // 1. Control del Sidebar Responsive
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');

        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && !sidebar.classList.contains('hidden') && !sidebar.contains(e.target) && e
                .target !== menuBtn) {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
            }
        });

        // 2. Interceptor global para formularios de eliminación con la clase .form-eliminar
        document.addEventListener('DOMContentLoaded', function() {
            const formularios = document.querySelectorAll('.form-eliminar');

            formularios.forEach(formulario => {
                formulario.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: "Confirmación de Seguridad",
                        text: "Esta acción es destructiva y eliminará las citas vinculadas. Por favor, ingresa tu contraseña de administrador para continuar:",
                        icon: "warning",
                        input: "password",
                        inputAttributes: {
                            autocapitalize: "off",
                            autocorrect: "off",
                            placeholder: "Contraseña de seguridad"
                        },
                        showCancelButton: true,
                        confirmButtonColor: "#f43f5e",
                        cancelButtonColor: "#64748b",
                        confirmButtonText: "Confirmar y Eliminar",
                        cancelButtonText: "Cancelar",
                        inputValidator: (value) => {
                            if (!value) {
                                return "¡Es obligatorio ingresar tu contraseña para autorizar la eliminación!";
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            const inputPassword = document.createElement('input');
                            inputPassword.type = 'hidden';
                            inputPassword.name = 'admin_password';
                            inputPassword.value = result.value;

                            this.appendChild(inputPassword);
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>

    {{-- 🚨 SECCIÓN CORREGIDA: Renderizado sin escapar {!! !!} para SweetAlert --}}
    @if (session('success'))
        <script>
            Swal.fire({
                text: "{!! session('success') !!}",
                icon: "success",
                draggable: true,
                confirmButtonColor: "#4f46e5"
            });
        </script>
    @endif
</body>

</html>
