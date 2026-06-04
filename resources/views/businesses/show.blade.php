<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios del Comercio - BookIt</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans antialiased">

    <!-- Navegación -->
    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl font-black text-indigo-600 tracking-tight">BookIt</span>
                <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-md">Empresarial</span>
            </div>
            <span class="text-sm text-slate-500 font-medium">Panel de Usuario</span>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-10">

        <!-- Botón Volver -->
        <a href="/negocios" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors mb-6 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al catálogo de comercios
        </a>

        <!-- Contenedor Principal en Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- COLUMNA IZQUIERDA: Info del Comercio y Lista de Servicios (Ocupa 2 columnas) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Banner Informativo del Comercio -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-indigo-600"></div>
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 capitalize tracking-tight">{{ str_replace('-', ' ', $slug) }}</h1>
                        <p class="mt-2 text-slate-600 text-sm">Selecciona uno de nuestros servicios profesionales especializados y agenda tu espacio.</p>
                    </div>
                </div>

                <!-- Lista de Servicios Disponibles -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200 bg-slate-50/70">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Portafolio de Servicios Disponibles</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @if(str_contains($slug, 'barberia'))
                        <!-- Servicio Barbería 1 -->
                        <div class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400" alt="Corte" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="font-bold text-slate-900 text-base">Corte de Cabello Premium</h3>
                                <p class="text-xs text-slate-600 mt-1">Asesoría de perfilado, lavado purificante y peinado profesional.</p>
                                <span class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️ 35 min</span>
                            </div>
                            <div class="text-center sm:text-right min-w-[100px]">
                                <p class="text-xl font-black text-indigo-600 mb-2">$10.00</p>
                                <button onclick="seleccionarServicio('Corte de Cabello Premium', '$10.00')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full">Reservar</button>
                            </div>
                        </div>

                        <!-- Servicio Barbería 2 -->
                        <div class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400" alt="Barba" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="font-bold text-slate-900 text-base">Ritual de Barba Clásico</h3>
                                <p class="text-xs text-slate-600 mt-1">Afeitado tradicional con navaja y toallas calientes humectantes.</p>
                                <span class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️ 30 min</span>
                            </div>
                            <div class="text-center sm:text-right min-w-[100px]">
                                <p class="text-xl font-black text-indigo-600 mb-2">$8.00</p>
                                <button onclick="seleccionarServicio('Ritual de Barba Clásico', '$8.00')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full">Reservar</button>
                            </div>
                        </div>

                        @elseif(str_contains($slug, 'clinica'))
                        <!-- Servicio Clínica -->
                        <div class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=400" alt="Consulta" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="font-bold text-slate-900 text-base">Consulta Médica General</h3>
                                <p class="text-xs text-slate-600 mt-1">Evaluación completa, control de signos vitales y receta médica.</p>
                                <span class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️ 30 min</span>
                            </div>
                            <div class="text-center sm:text-right min-w-[100px]">
                                <p class="text-xl font-black text-indigo-600 mb-2">$25.00</p>
                                <button onclick="seleccionarServicio('Consulta Médica General', '$25.00')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full">Reservar</button>
                            </div>
                        </div>

                        @else
                        <!-- Servicio por Defecto (Taller / Automotriz) -->
                        <div class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?w=400" alt="Aceite" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="font-bold text-slate-900 text-base">Cambio de Aceite y Filtros</h3>
                                <p class="text-xs text-slate-600 mt-1">Lubricante sintético de alto rendimiento y cambio de filtros.</p>
                                <span class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️ 45 min</span>
                            </div>
                            <div class="text-center sm:text-right min-w-[100px]">
                                <p class="text-xl font-black text-indigo-600 mb-2">$45.00</p>
                                <button onclick="seleccionarServicio('Cambio de Aceite y Filtros', '$45.00')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full">Reservar</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <p class="text-center text-xs text-slate-400 pt-6">BookIt Empresarial © 2026. Todos los derechos reservados.</p>
            </div>

            <!-- COLUMNA DERECHA: Panel Dinámico de Agenda de Reserva (Ocupa 1 columna) -->
            <div>
                <div id="panel-reserva" class="hidden sticky top-24">
                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
                        <h2 class="text-xl font-black text-slate-900 tracking-tight border-b border-slate-100 pb-4">Agendar Cita</h2>

                        <div class="mt-4 bg-indigo-50 rounded-xl p-3 text-sm">
                            <p class="text-slate-500 font-medium">Servicio:</p>
                            <p id="resumen-servicio" class="font-bold text-indigo-900 text-base">Ninguno</p>
                            <p id="resumen-precio" class="text-indigo-600 font-black mt-0.5">$0.00</p>
                        </div>

                        <form action="{{ route('appointments.store') }}" method="POST" class="mt-6 space-y-4">
                            @csrf

                            <!-- Inputs Ocultos Requeridos por el Formulario -->
                            <input type="hidden" name="servicio_nombre" id="input-servicio-nombre" required>
                            <input type="hidden" name="servicio_precio" id="input-servicio-precio" required>
                            <input type="hidden" name="business_slug" value="{{ $slug }}">

                            <!-- Fecha de la Cita -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">1. Selecciona el Día</label>
                                <input type="date" name="fecha_cita" required min="{{ date('Y-m-d') }}"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            </div>

                            <!-- Horarios Disponibles -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">2. Selecciona la Hora</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="09:00" class="sr-only peer" required>
                                        <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">09:00 AM</span>
                                    </label>
                                    <label class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="10:30" class="sr-only peer">
                                        <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">10:30 AM</span>
                                    </label>
                                    <label class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="14:00" class="sr-only peer">
                                        <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">02:00 PM</span>
                                    </label>
                                    <label class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="15:30" class="sr-only peer">
                                        <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">03:30 PM</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Botón de Envío -->
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3.5 rounded-xl shadow-sm transition-all text-center block">
                                    Confirmar y Agendar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div> <!-- Cierre del Grid Principal -->
    </div> <!-- Cierre del Contenedor -->

    <!-- Lógica de Frontend en JavaScript -->
    <script>
        function seleccionarServicio(nombre, precio) {
            // Muestra el panel lateral de reserva
            document.getElementById('panel-reserva').classList.remove('hidden');

            // Renderiza los datos en la interfaz del ticket de previsualización
            document.getElementById('resumen-servicio').innerText = nombre;
            document.getElementById('resumen-precio').innerText = precio;

            // Inserta los valores correspondientes en los inputs nativos del formulario
            document.getElementById('input-servicio-nombre').value = nombre;
            document.getElementById('input-servicio-precio').value = precio.replace('$', '');

            // Realiza scroll suave hacia el panel en pantallas móviles
            document.getElementById('panel-reserva').scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>

    <!-- Estilos Personalizados para los Radio Buttons con Tailwind -->
    <style>
        input[type="radio"]:checked+span {
            color: #4f46e5 !important;
        }

        label:has(input[type="radio"]:checked) {
            border-color: #4f46e5 !important;
            background-color: #eef2ff !important;
        }
    </style>
</body>

</html>