<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios del Comercio - BookIt</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans antialiased">

    <!-- Navbar Principal -->
    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl font-black text-indigo-600 tracking-tight">BookIt</span>
                <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded-md">Empresarial</span>
            </div>
            <span class="text-sm text-slate-500 font-medium">Panel de Usuario</span>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-10">

        <!-- Botón Volver -->
        <a href="/negocios"
            class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors mb-6 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Volver al catálogo de comercios
        </a>

        <!-- Banner de Perfil Limpio -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-indigo-600"></div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 capitalize tracking-tight">
                    {{ str_replace('-', ' ', $slug) }}</h1>
                <p class="mt-2 text-slate-600 text-sm">Selecciona uno de nuestros servicios profesionales especializados
                    y agenda tu espacio.</p>
            </div>
        </div>

        <!-- Portafolio Corporativo -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Portafolio de Servicios Disponibles</h2>
            </div>

            <div class="divide-y divide-slate-200">

                <!-- CASO 1: BARBERÍA -->
                @if (str_contains($slug, 'barberia'))
                    <!-- Servicio 1 -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400"
                                alt="Corte Premium" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Corte de Cabello Premium</h3>
                            <p class="text-sm text-slate-600 mt-1">Asesoría de perfilado según fisionomía, lavado champú
                                purificante y peinado profesional.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                35 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$10.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- Servicio 2 -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400"
                                alt="Ritual Barba" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Ritual de Barba Clásico</h3>
                            <p class="text-sm text-slate-600 mt-1">Afeitado tradicional con navaja, toallas calientes
                                humectantes y loción refrescante de poros.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                30 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$8.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- Servicio 3 -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=400"
                                alt="Combo Ejecutivo" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Combo Executive (Corte + Barba)</h3>
                            <p class="text-sm text-slate-600 mt-1">Servicio completo premium que unifica el corte
                                estilizado junto con el tratamiento integral de barba.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                60 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$16.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- CASO 2: CLÍNICA -->
                @elseif(str_contains($slug, 'clinica'))
                    <!-- Servicio 1 -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=400"
                                alt="Consulta General" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Consulta Médica General</h3>
                            <p class="text-sm text-slate-600 mt-1">Evaluación completa, control de signos vitales,
                                recetas certificadas y diagnóstico preventivo.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                30 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$25.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- Servicio 2 (FOTO CAMBIADA A PICSUM INDUSTRIAL) -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://picsum.photos/id/613/200/200" alt="Control Pediatrico"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Control Pediátrico Integral</h3>
                            <p class="text-sm text-slate-600 mt-1">Evaluación del crecimiento, control nutricional,
                                revisión de esquema vacunal e informe de desarrollo.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                40 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$30.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- Servicio 3 (FOTO CAMBIADA A PICSUM INDUSTRIAL) -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://picsum.photos/id/219/200/200" alt="Chequeo Ejecutivo"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Chequeo Médico Ejecutivo</h3>
                            <p class="text-sm text-slate-600 mt-1">Análisis clínico metabólico completo guiado junto con
                                lectura técnica de electrocardiograma basal.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                60 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$50.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- CASO 3: TALLER (MECÁNICA) -->
                @else
                    <!-- Servicio 1 -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?w=400"
                                alt="Cambio de Aceite" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Cambio de Aceite y Filtros</h3>
                            <p class="text-sm text-slate-600 mt-1">Sustitución de lubricante sintético de motor de alto
                                rendimiento, filtro de aceite y filtro de aire base.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                45 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$45.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- Servicio 2 (FOTO CORREGIDA CON PICSUM ULTRAESTABLE) -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://picsum.photos/id/1071/200/200" alt="Mantenimiento Frenos"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Mantenimiento de Frenos</h3>
                            <p class="text-sm text-slate-600 mt-1">Desmontaje integral de pastillas, rectificación de
                                discos o bandas hidráulicas y purga del líquido.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                50 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$25.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>

                    <!-- Servicio 3 (FOTO CAMBIADA A PICSUM INDUSTRIAL) -->
                    <div
                        class="p-6 flex flex-col md:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="https://picsum.photos/id/443/200/200" alt="Scanner Computarizado"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="font-bold text-slate-900 text-lg">Escáner Computarizado</h3>
                            <p class="text-sm text-slate-600 mt-1">Lectura técnica completa de códigos de error de la
                                ECU del motor, borrado de alertas y diagnóstico impreso.</p>
                            <span
                                class="inline-block mt-2 text-xs text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                30 min</span>
                        </div>
                        <div class="text-center md:text-right min-w-[120px]">
                            <p class="text-2xl font-black text-indigo-600 mb-2">$20.00</p>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-4 py-2 rounded-xl shadow-sm transition-colors">Reservar</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-8">BookIt Empresarial © 2026. Todos los derechos reservados.
        </p>
    </div>
</body>

</html>
