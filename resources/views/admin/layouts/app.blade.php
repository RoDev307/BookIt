<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel de Administración - BookIt</title>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ==========================================================================
           MODO DÍA (CLARO)
           ========================================================================== */
        :root[data-theme="light"],
        :root[data-theme="light"] body {
            background-color: #f3f4f6 !important;
            /* bg-gray-100 */
            color: #1e293b !important;
        }

        :root[data-theme="light"] header {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
        }

        :root[data-theme="light"] header h2 {
            color: #374151 !important;
        }

        :root[data-theme="light"] header span {
            color: #4b5563 !important;
        }

        :root[data-theme="light"] .bg-white,
        :root[data-theme="light"] div[class*="bg-white"] {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
        }

        :root[data-theme="light"] td,
        :root[data-theme="light"] th {
            color: #1e293b !important;
        }

        /* ==========================================================================
           MODO NOCHE (OSCURO) - VERSIÓN COLORIDA Y PREMIUM
           ========================================================================== */
        :root[data-theme="dark"],
        :root[data-theme="dark"] body {
            background-color: #0b0f19 !important;
            /* Un azul espacial más profundo que resalta los colores */
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] header,
        :root[data-theme="dark"] .bg-white,
        :root[data-theme="dark"] div[class*="bg-white"],
        :root[data-theme="dark"] main form,
        :root[data-theme="dark"] main div.bg-white,
        :root[data-theme="dark"] div[class*="rounded-2xl"] {
            background-color: #131c2e !important;
            /* Slate 800 enriquecido con tono azulado */
            border-color: #1e293b !important;
        }

        :root[data-theme="dark"] header h2 {
            color: #ffffff !important;
        }

        :root[data-theme="dark"] header span {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] .bg-slate-50\/70,
        :root[data-theme="dark"] .bg-slate-50 {
            background-color: #172237 !important;
            border-bottom: 1px solid #22314d !important;
        }

        :root[data-theme="dark"] tr:hover {
            background-color: rgba(79, 70, 229, 0.08) !important;
            /* Efecto hover con destello índigo */
        }

        :root[data-theme="dark"] th {
            color: #64748b !important;
        }

        :root[data-theme="dark"] td {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] h1,
        :root[data-theme="dark"] h2,
        :root[data-theme="dark"] h3,
        :root[data-theme="dark"] main h1,
        :root[data-theme="dark"] main h2,
        :root[data-theme="dark"] main h3,
        :root[data-theme="dark"] .text-slate-900,
        :root[data-theme="dark"] .text-gray-700 {
            color: #ffffff !important;
        }

        :root[data-theme="dark"] p,
        :root[data-theme="dark"] .text-slate-600,
        :root[data-theme="dark"] .text-gray-600 {
            color: #94a3b8 !important;
        }

        /* 🟢 COLORIZACIÓN DE MÉTRICAS OPERATIVAS EN MODO OSCURO */
        :root[data-theme="dark"] h3.text-emerald-600,
        :root[data-theme="dark"] .text-emerald-600 {
            color: #10b981 !important;
            /* Verde esmeralda vivo */
        }

        :root[data-theme="dark"] h3.text-rose-600,
        :root[data-theme="dark"] .text-rose-600 {
            color: #f43f5e !important;
            /* Rosa/rojo vivo corporativo */
        }

        :root[data-theme="dark"] h3.text-2xl.font-black,
        :root[data-theme="dark"] .grid h3 {
            color: #ffffff !important;
        }

        /* 🚨 RECTIFICACIÓN PREMIUM DE ICONOS DE MÉTRICAS (Efecto neón translúcido colorido) */
        :root[data-theme="dark"] .bg-blue-50,
        :root[data-theme="dark"] div[class*="bg-blue-50"] {
            background-color: rgba(59, 130, 246, 0.15) !important;
            /* Azul translúcido */
            border: 1px solid rgba(59, 130, 246, 0.3) !important;
            color: #60a5fa !important;
        }

        :root[data-theme="dark"] .bg-indigo-50,
        :root[data-theme="dark"] div[class*="bg-indigo-50"] {
            background-color: rgba(99, 102, 241, 0.15) !important;
            /* Índigo translúcido */
            border: 1px solid rgba(99, 102, 241, 0.3) !important;
            color: #818cf8 !important;
        }

        :root[data-theme="dark"] .bg-emerald-50,
        :root[data-theme="dark"] div[class*="bg-emerald-50"] {
            background-color: rgba(16, 185, 129, 0.15) !important;
            /* Esmeralda translúcido */
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
            color: #34d399 !important;
        }

        :root[data-theme="dark"] .bg-rose-50,
        :root[data-theme="dark"] div[class*="bg-rose-50"] {
            background-color: rgba(244, 63, 94, 0.15) !important;
            /* Rosa translúcido */
            border: 1px solid rgba(244, 63, 94, 0.3) !important;
            color: #f87171 !important;
        }

        /* Estilos generales de inputs y labels */
        :root[data-theme="dark"] label,
        :root[data-theme="dark"] .text-slate-700,
        :root[data-theme="dark"] div[class*="text-slate-700"],
        :root[data-theme="dark"] div[class*="text-xs"],
        :root[data-theme="dark"] div[class*="text-[10px]"] {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] input,
        :root[data-theme="dark"] select,
        :root[data-theme="dark"] textarea {
            background-color: #0b0f19 !important;
            border-color: #22314d !important;
            color: #ffffff !important;
        }

        :root[data-theme="dark"] input::placeholder,
        :root[data-theme="dark"] textarea::placeholder {
            color: #475569 !important;
        }

        :root[data-theme="dark"] .text-indigo-600,
        :root[data-theme="dark"] a[href*="edit"],
        :root[data-theme="dark"] .text-blue-600 {
            color: #a5b4fc !important;
        }

        :root[data-theme="dark"] button:not([type="submit"]),
        :root[data-theme="dark"] .border-slate-200,
        :root[data-theme="dark"] a[class*="border"],
        :root[data-theme="dark"] button[class*="bg-slate-100"],
        :root[data-theme="dark"] a[class*="bg-slate-100"] {
            background-color: #172237 !important;
            border-color: #22314d !important;
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] button:not([type="submit"]):hover,
        :root[data-theme="dark"] a[class*="bg-slate-100"]:hover {
            background-color: #22314d !important;
            color: #ffffff !important;
        }

        :root[data-theme="dark"] label:has(input[type="radio"]:checked) {
            background-color: #1e1b4b !important;
            border-color: #6366f1 !important;
        }

        :root[data-theme="dark"] label:has(input[type="radio"]:checked) span {
            color: #818cf8 !important;
        }

        :root[data-theme="dark"] .fc {
            --fc-page-bg-color: #131c2e !important;
            --fc-border-color: #22314d !important;
            --fc-neutral-text-color: #ffffff !important;
        }

        :root[data-theme="dark"] .fc-theme-standard td,
        :root[data-theme="dark"] .fc-theme-standard th,
        :root[data-theme="dark"] .fc-theme-standard .fc-scrollgrid {
            border-color: #22314d !important;
        }

        :root[data-theme="dark"] .fc .fc-col-header-cell-cushion,
        :root[data-theme="dark"] .fc .fc-daygrid-day-number,
        :root[data-theme="dark"] .fc .fc-toolbar-title,
        :root[data-theme="dark"] h2[class*="fc-toolbar-title"] {
            color: #ffffff !important;
        }

        :root[data-theme="dark"] .fc .fc-button-primary {
            background-color: #172237 !important;
            border-color: #22314d !important;
            color: #ffffff !important;
        }

        :root[data-theme="dark"] .fc .fc-button-primary:hover,
        :root[data-theme="dark"] .fc .fc-button-active {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <aside id="sidebar"
        class="w-64 bg-slate-800 text-white flex flex-col justify-between hidden md:fixed md:inset-y-0 md:left-0 md:flex z-50">
        <div class="p-5">
            <h1 class="text-2xl font-bold tracking-wider text-indigo-400 mb-8">BookIt Admin</h1>
            <nav class="space-y-2">
                @if (!is_null(Auth::user()->business_id))
                    <a href="{{ route('dashboard') }}"
                        class="block py-2.5 px-4 rounded transition {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📅 Gestionar Citas
                    </a>
                    <a href="{{ route('admin.appointments.create') }}"
                        class="block py-2.5 px-4 rounded transition {{ request()->routeIs('admin.appointments.create') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        ⚡ Agendar Cita
                    </a>
                    <a href="{{ route('admin.appointments.calendar') }}"
                        class="block py-2.5 px-4 rounded transition {{ request()->routeIs('admin.appointments.calendar') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        |&nbsp;📆 Calendario Operativo
                    </a>
                    <a href="{{ route('services.index') }}"
                        class="block py-2.5 px-4 rounded transition {{ request()->routeIs('services.*') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        🛠️ Gestionar Servicios
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="block py-2.5 px-4 rounded transition {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📊 Métricas
                    </a>
                @endif

                @if (Auth::user()->email === 'admin@bookit.com')
                    <div class="mt-6 pt-4 border-t border-slate-700">
                        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                            Administrador Maestro
                        </p>
                        <a href="{{ route('master.businesses.index') }}"
                            class="flex items-center justify-between py-2.5 px-4 rounded transition font-bold {{ request()->routeIs('master.businesses.*') ? 'bg-indigo-900 text-white' : 'text-indigo-300 hover:bg-slate-700 hover:text-indigo-200' }}">
                            <span>Controlar Comercios</span>
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

        <header
            class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b border-gray-200 transition-colors">
            <button id="menu-btn"
                class="md:hidden text-gray-600 focus:outline-none text-xl cursor-pointer p-1 rounded hover:bg-gray-100">
                ☰
            </button>

            <h2 class="text-xl font-semibold text-gray-700">Consola de Control</h2>

            <div class="flex items-center space-x-4">
                {{-- Slider Switch --}}
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-700/50 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-600 transition-colors"
                    id="admin-slider-container">
                    <span class="text-xs select-none">☀️</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="admin-theme-slider" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-300 rounded-full transition-colors after:content-[''] after:absolute after:top-[2px] after:bg-white after:border-slate-300 dark:border-slate-600 after:border after:rounded-full after:h-4 after:w-4 after:transition-all"
                            id="admin-slider-bg"></div>
                    </label>
                    <span class="text-xs select-none">🌙</span>
                </div>

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

        document.addEventListener("DOMContentLoaded", function() {
            const adminSlider = document.getElementById('admin-theme-slider');
            const adminSliderBg = document.getElementById('admin-slider-bg');
            const adminContainer = document.getElementById('admin-slider-container');
            const isDark = localStorage.getItem('theme') === 'dark';

            if (isDark) {
                adminSlider.checked = true;
                if (adminSliderBg) {
                    adminSliderBg.style.backgroundColor = '#4f46e5';
                    adminSliderBg.classList.add('after:left-[20px]');
                    adminSliderBg.classList.remove('after:left-[2px]');
                }
                if (adminContainer) {
                    adminContainer.style.backgroundColor = '#131c2e';
                    adminContainer.style.borderColor = '#1e293b';
                }
            } else {
                adminSlider.checked = false;
                if (adminSliderBg) {
                    adminSliderBg.style.backgroundColor = '#cbd5e1';
                    adminSliderBg.classList.add('after:left-[2px]');
                    adminSliderBg.classList.remove('after:left-[20px]');
                }
                if (adminContainer) {
                    adminContainer.style.backgroundColor = '#f3f4f6';
                    adminContainer.style.borderColor = '#e5e7eb';
                }
            }

            adminSlider.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
                window.location.reload();
            });
        });
    </script>

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
