@extends('layouts.public')

@section('title', 'Servicios del Comercio - BookIt')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-10">

        <a href="{{ route('businesses.index') }}"
            class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors mb-6 group">
            <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al catálogo de comercios
        </a>

        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl shadow-sm">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-indigo-600"></div>
                    <div>
                        <h1 class="text-text-3xl font-black text-slate-900 capitalize tracking-tight">
                            {{ $business->name }}
                        </h1>
                        <p class="mt-2 text-slate-600 text-sm">
                            Selecciona uno de nuestros servicios profesionales especializados y agenda tu espacio en
                            {{ $business->name }}.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200 bg-slate-50/70">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Portafolio de Servicios Disponibles</h2>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @if (str_contains($slug, 'barberia'))
                            <div
                                class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                    <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400"
                                        alt="Corte" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="font-bold text-slate-900 text-base">Corte de Cabello Premium</h3>
                                    <p class="text-xs text-slate-600 mt-1">Asesoría de perfilado, lavado purificante y
                                        peinado profesional.</p>
                                    <span
                                        class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                        35 min</span>
                                </div>
                                <div class="text-center sm:text-right min-w-[100px]">
                                    <p class="text-xl font-black text-indigo-600 mb-2">$10.00</p>
                                    <button onclick="seleccionarServicio('Corte de Cabello Premium', '$10.00')"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full cursor-pointer">Reservar</button>
                                </div>
                            </div>

                            <div
                                class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                    <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=400"
                                        alt="Barba" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="font-bold text-slate-900 text-base">Ritual de Barba Clásico</h3>
                                    <p class="text-xs text-slate-600 mt-1">Afeitado tradicional con navaja y toallas
                                        calientes humectantes.</p>
                                    <span
                                        class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                        30 min</span>
                                </div>
                                <div class="text-center sm:text-right min-w-[100px]">
                                    <p class="text-xl font-black text-indigo-600 mb-2">$8.00</p>
                                    <button onclick="seleccionarServicio('Ritual de Barba Clásico', '$8.00')"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full cursor-pointer">Reservar</button>
                                </div>
                            </div>
                        @elseif(str_contains($slug, 'clinica'))
                            <div
                                class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=400"
                                        alt="Consulta" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="font-bold text-slate-900 text-base">Consulta Médica General</h3>
                                    <p class="text-xs text-slate-600 mt-1">Evaluación completa, control de signos vitales y
                                        receta médica.</p>
                                    <span
                                        class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                        30 min</span>
                                </div>
                                <div class="text-center sm:text-right min-w-[100px]">
                                    <p class="text-xl font-black text-indigo-600 mb-2">$25.00</p>
                                    <button onclick="seleccionarServicio('Consulta Médica General', '$25.00')"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full cursor-pointer">Reservar</button>
                                </div>
                            </div>
                        @else
                            <div
                                class="p-6 flex flex-col sm:flex-row gap-6 hover:bg-slate-50/50 transition-colors items-center">
                                <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                                    <img src="https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?w=400"
                                        alt="Aceite" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="font-bold text-slate-900 text-base">Cambio de Aceite y Filtros</h3>
                                    <p class="text-xs text-slate-600 mt-1">Lubricante sintético de alto rendimiento y cambio
                                        de filtros.</p>
                                    <span
                                        class="inline-block mt-2 text-[11px] text-slate-500 font-medium bg-slate-100 px-2 py-0.5 rounded">⏱️
                                        45 min</span>
                                </div>
                                <div class="text-center sm:text-right min-w-[100px]">
                                    <p class="text-xl font-black text-indigo-600 mb-2">$45.00</p>
                                    <button onclick="seleccionarServicio('Cambio de Aceite y Filtros', '$45.00')"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-colors w-full cursor-pointer">Reservar</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <p class="text-center text-xs text-slate-400 pt-6">BookIt Empresarial © 2026. Todos los derechos reservados.
                </p>
            </div>

            <div>
                <div id="panel-reserva" class="hidden sticky top-24">
                    <div class="bg-white rounded-2xl shadow-md border border-slate-200 p-6">
                        <h2 class="text-xl font-black text-slate-900 tracking-tight border-b border-slate-100 pb-4">Agendar
                            Cita</h2>

                        <div class="mt-4 bg-indigo-50 rounded-xl p-3 text-sm">
                            <p class="text-slate-500 font-medium">Servicio:</p>
                            <p id="resumen-servicio" class="font-bold text-indigo-900 text-base">Ninguno</p>
                            <p id="resumen-precio" class="text-indigo-600 font-black mt-0.5">$0.00</p>
                        </div>

                        <form action="{{ route('appointments.store') }}" method="POST" class="mt-6 space-y-4">
                            @csrf

                            <input type="hidden" name="servicio_nombre" id="input-servicio-nombre" required>
                            <input type="hidden" name="servicio_precio" id="input-servicio-precio" required>
                            <input type="hidden" name="business_slug" value="{{ $slug }}">

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">1.
                                    Selecciona el Día</label>
                                <input type="date" name="fecha_cita" required min="{{ date('Y-m-d') }}"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">2.
                                    Selecciona la Hora</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label
                                        class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="09:00" class="sr-only peer"
                                            required>
                                        <span
                                            class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">09:00
                                            AM</span>
                                    </label>
                                    <label
                                        class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="10:30" class="sr-only peer">
                                        <span
                                            class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">10:30
                                            AM</span>
                                    </label>
                                    <label
                                        class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="14:00" class="sr-only peer">
                                        <span
                                            class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">02:00
                                            PM</span>
                                    </label>
                                    <label
                                        class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-colors block">
                                        <input type="radio" name="hora_cita" value="15:30" class="sr-only peer">
                                        <span
                                            class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">03:30
                                            PM</span>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3.5 rounded-xl shadow-sm transition-all text-center block cursor-pointer">
                                    Confirmar y Agendar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function seleccionarServicio(nombre, precio) {
            document.getElementById('panel-reserva').classList.remove('hidden');
            document.getElementById('resumen-servicio').innerText = nombre;
            document.getElementById('resumen-precio').innerText = precio;
            document.getElementById('input-servicio-nombre').value = nombre;
            document.getElementById('input-servicio-precio').value = precio.replace('$', '');
            document.getElementById('panel-reserva').scrollIntoView({
                behavior: 'smooth'
            });
        }
    </script>

    <style>
        input[type="radio"]:checked+span {
            color: #4f46e5 !important;
        }

        label:has(input[type="radio"]:checked) {
            border-color: #4f46e5 !important;
            background-color: #eef2ff !important;
        }
    </style>
@endsection
