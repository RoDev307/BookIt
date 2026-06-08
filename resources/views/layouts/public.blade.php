<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookIt - Sistema de Citas')</title>

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
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

    {{-- Script CDN de Tailwind v4 --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        /* ==========================================================================
           MODO DÍA (CLARO) FORZADO
           ========================================================================== */
        :root[data-theme="light"],
        :root[data-theme="light"] body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        :root[data-theme="light"] nav {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        :root[data-theme="light"] nav a {
            color: #475569 !important;
        }

        :root[data-theme="light"] nav a.text-indigo-600,
        :root[data-theme="light"] nav strong {
            color: #4f46e5 !important;
        }

        :root[data-theme="light"] .bg-white {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        :root[data-theme="light"] h1,
        :root[data-theme="light"] h3 {
            color: #0f172a !important;
        }

        :root[data-theme="light"] p {
            color: #334155 !important;
        }

        :root[data-theme="light"] .bg-slate-50 {
            background-color: #f8fafc !important;
            border-color: #f1f5f9 !important;
        }

        :root[data-theme="light"] footer {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        /* Corregir opacidades en Modo Claro */
        :root[data-theme="light"] .bg-indigo-50 {
            background-color: #e0e7ff !important;
            border-color: #c7d2fe !important;
        }

        :root[data-theme="light"] .text-indigo-700 {
            color: #4338ca !important;
        }

        :root[data-theme="light"] .text-indigo-600 {
            color: #4f46e5 !important;
        }

        :root[data-theme="light"] nav a.bg-indigo-600,
        :root[data-theme="light"] a[class*="bg-indigo-600"] {
            color: #ffffff !important;
        }

        /* ==========================================================================
           MODO NOCHE (OSCURO) FORZADO PREMIUM
           ========================================================================== */
        :root[data-theme="dark"],
        :root[data-theme="dark"] body {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }

        :root[data-theme="dark"] nav {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] nav a {
            color: #cbd5e1 !important;
        }

        :root[data-theme="dark"] nav a.dark\:text-indigo-400,
        :root[data-theme="dark"] nav strong {
            color: #818cf8 !important;
        }

        :root[data-theme="dark"] .bg-white {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] h1,
        :root[data-theme="dark"] h3,
        :root[data-theme="dark"] footer h4 {
            color: #ffffff !important;
            /* Forzar blanco puro a títulos del footer */
        }

        :root[data-theme="dark"] p,
        :root[data-theme="dark"] footer p,
        :root[data-theme="dark"] footer li {
            color: #94a3b8 !important;
            /* Gris claro nítido para textos y viñetas del footer */
        }

        :root[data-theme="dark"] .bg-slate-50 {
            background-color: #1e293b80 !important;
            border-color: #33415580 !important;
        }

        :root[data-theme="dark"] footer {
            background-color: #1e293b80 !important;
            border-color: #334155 !important;
        }

        /* Ajuste de opacidades en Modo Oscuro */
        :root[data-theme="dark"] .bg-indigo-50 {
            background-color: rgba(30, 27, 75, 0.4) !important;
            border-color: rgba(67, 56, 202, 0.4) !important;
        }

        :root[data-theme="dark"] .text-indigo-700 {
            color: #a5b4fc !important;
        }

        /* 🚨 RECTIFICACIÓN DE ALTA PRIORIDAD PARA ELEMENTOS DEL FOOTER OSCURO MARCADOS EN CAPTURA */
        :root[data-theme="dark"] footer .font-mono.text-indigo-600 {
            background-color: #1e1b4b !important;
            /* Fondo Indigo 950 para tu nombre */
            border-color: #4338ca !important;
            /* Borde Indigo 700 */
            color: #a5b4fc !important;
            /* Texto claro nítido */
        }

        :root[data-theme="dark"] footer div[class*="border-t"] {
            border-color: #334155 !important;
            /* Línea separadora divisoria nítida */
        }

        :root[data-theme="dark"] footer span[class*="bg-emerald-50"] {
            background-color: rgba(6, 78, 59, 0.3) !important;
            /* Fondo badge clúster */
            border-color: rgba(16, 185, 129, 0.4) !important;
            color: #34d399 !important;
            /* Texto verde brillante clúster */
        }
    </style>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800 transition-colors duration-200">

    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('businesses.index') }}"
                    class="text-2xl font-black text-indigo-600 tracking-tight">BookIt</a>
                <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('businesses.index') }}"
                        class="text-indigo-600 border-b-2 border-indigo-600 px-1 py-5">Inicio</a>
                    @auth
                        @if (Auth::user()->role === 'client')
                            <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition-colors">Mis
                                Reservas</a>
                        @endif

                        @if (Auth::user()->role === 'admin_business' ||
                                Auth::user()->role === 'super_admin' ||
                                Auth::user()->email === 'admin@bookit.com')
                            <a href="{{ route('dashboard') }}"
                                class="text-indigo-500 font-semibold hover:text-indigo-700 transition-colors">
                                Panel Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="flex items-center space-x-4">
                {{-- 🎛 shrink slider --}}
                <div class="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200 transition-colors"
                    id="slider-container">
                    <span class="text-xs select-none">☀️</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="public-theme-slider" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-300 rounded-full transition-colors after:content-[''] after:absolute after:top-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all"
                            id="slider-bg"></div>
                    </label>
                    <span class="text-xs select-none">🌙</span>
                </div>

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
                        class="text-sm font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl transition-colors shadow-sm">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 mt-24 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <span
                        class="text-lg font-black dark:text-indigo-500 tracking-tight text-slate-900 flex items-center gap-2">
                        📅 BookIt<span
                            class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider">SaaS</span>
                    </span>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Infraestructura en la nube distribuida multi-tenant, optimizada para la gestión cronológica,
                        asignación de personal y automatización operativa de comercios locales.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Solución SaaS</h4>
                    <ul class="space-y-2.5 text-xs font-medium text-slate-600">
                        <li>✓ Agendamiento Manual de Comercio</li>
                        <li>✓ Control de Disponibilidad y Horarios</li>
                        <li>✓ Evitación de Colisiones en Tiempo Real</li>
                        <li>✓ Calendario Sincronizado Operativo</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Infraestructura</h4>
                    <ul class="space-y-2.5 text-xs font-medium text-slate-600">
                        <li><span class="font-bold">Motor DB:</span> MySQL (Aiven Cloud)</li>
                        <li><span class="font-bold">Framework:</span> Laravel 11 / PHP 8.2</li>
                        <li><span class="font-bold">Frontend UI:</span> Tailwind CSS v4 / Blade</li>
                        <li><span class="font-bold">Integración:</span> FullCalendar.js API</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Proyecto Académico</h4>
                    <div class="text-xs text-slate-600 space-y-2 font-medium">
                        <p class="font-bold text-slate-800">Desarrollado por:</p>
                        <p
                            class="font-mono text-indigo-600 font-bold text-sm bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-100 inline-block">
                            Equipo Rodrigo
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            ITCA FEPADE / Desarrollo de Aplicaciones Web. Proyecto de evaluación de sistemas
                            multi-inquilino.
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="mt-12 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-400">
                <p>&copy; 2026 BookIt Plataforma SaaS. Desarrollado en El Salvador con fines estrictamente educativos.
                </p>
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Clúster MySQL
                        Activo (Aiven)
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const themeSlider = document.getElementById('public-theme-slider');
            const sliderBg = document.getElementById('slider-bg');
            const container = document.getElementById('slider-container');
            const isDark = localStorage.getItem('theme') === 'dark';

            if (isDark) {
                themeSlider.checked = true;
                sliderBg.style.backgroundColor = '#4f46e5';
                sliderBg.classList.add('after:left-[20px]');
                sliderBg.classList.remove('after:left-[2px]');
                if (container) {
                    container.style.backgroundColor = '#1e293b';
                    container.style.borderColor = '#334155';
                }
            } else {
                themeSlider.checked = false;
                sliderBg.style.backgroundColor = '#cbd5e1';
                sliderBg.classList.add('after:left-[2px]');
                sliderBg.classList.remove('after:left-[20px]');
                if (container) {
                    container.style.backgroundColor = '#f1f5f9';
                    container.style.borderColor = '#cbd5e1';
                }
            }

            themeSlider.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
                window.location.reload();
            });
        });
    </script>
</body>

</html>
