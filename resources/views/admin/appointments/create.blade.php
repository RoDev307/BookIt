@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Módulo de Agendamiento Manual</h3>
            <p class="text-xs text-slate-500 mt-0.5">Utiliza este formulario para ingresar citas recibidas de forma
                presencial, llamadas telefónicas o mensajes de WhatsApp.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-xl">
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.appointments.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre del
                    Cliente</label>
                <input type="text" name="client_name" value="{{ old('client_name') }}" required
                    placeholder="Ej. Juan Pérez"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Seleccionar
                    Servicio</label>
                <select name="service_id" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                    <option value="">-- Seleccione un servicio del catálogo --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }} (${{ number_format($service->price, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">¿Quién atiende?
                    (Especialista / Empleado)</label>
                <input type="text" name="staff_name" value="{{ old('staff_name') }}" required
                    placeholder="Ej. Dr. Armando Mendoza o Mca. Carlos"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">1. Selecciona la
                        Fecha</label>
                    <input type="date" name="fecha_cita" value="{{ old('fecha_cita') }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">2. Selecciona la
                        Hora</label>

                    {{-- Grilla operativa extendida hora por hora --}}
                    <div class="grid grid-cols-2 gap-2" id="bloques-horarios">
                        @php
                            $horarios = [
                                '08:00' => '08:00 AM',
                                '09:00' => '09:00 AM',
                                '10:00' => '10:00 AM',
                                '11:00' => '11:00 AM',
                                '12:00' => '12:00 PM',
                                '13:00' => '01:00 PM',
                                '14:00' => '02:00 PM',
                                '15:00' => '03:00 PM',
                                '16:00' => '04:00 PM',
                            ];
                        @endphp

                        @foreach ($horarios as $value => $label)
                            <label
                                class="border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                                <input type="radio" name="hora_cita" value="{{ $value }}"
                                    class="sr-only peer radio-bloque" required
                                    {{ old('hora_cita') == $value ? 'checked' : '' }}>
                                <span
                                    class="text-xs font-semibold text-slate-700 peer-checked:text-indigo-600">{{ $label }}</span>
                            </label>
                        @endforeach

                        {{-- Switcher de entrada personalizada --}}
                        <button type="button" id="btn-otra-hora"
                            class="border border-dashed border-slate-300 rounded-xl p-2.5 text-center text-xs font-bold text-indigo-600 hover:bg-indigo-50/50 transition-all cursor-pointer col-span-2 sm:col-span-1">
                            ➕ Otra hora...
                        </button>
                    </div>

                    {{-- Contenedor de Hora Personalizada --}}
                    <div id="contenedor-hora-personalizada"
                        class="hidden mt-3 p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Hora Exacta:</span>
                            <button type="button" id="btn-volver-bloques"
                                class="text-[11px] font-bold text-rose-500 hover:underline cursor-pointer">
                                Ver bloques
                            </button>
                        </div>
                        <input type="time" id="input-hora-personalizada" min="08:00" max="17:00"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notas
                    adicionales</label>
                <textarea name="notes" rows="3"
                    placeholder="Ej. El cliente solicita que le atienda un operario en específico..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex gap-3 border-t border-slate-100 mt-6">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">
                    Agendar Cita
                </button>
                <a href="{{ route('dashboard') }}"
                    class="border border-slate-200 text-slate-600 text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    {{-- Intercambiador reactivo de parámetros hora_cita --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnOtraHora = document.getElementById('btn-otra-hora');
            const btnVolverBloques = document.getElementById('btn-volver-bloques');
            const contenedorPersonalizado = document.getElementById('contenedor-hora-personalizada');
            const inputPersonalizado = document.getElementById('input-hora-personalizada');
            const radiosBloque = document.querySelectorAll('.radio-bloque');

            btnOtraHora.addEventListener('click', function() {
                contenedorPersonalizado.classList.remove('hidden');
                btnOtraHora.classList.add('hidden');

                radiosBloque.forEach(radio => {
                    radio.checked = false;
                    radio.required = false;
                });

                inputPersonalizado.name = 'hora_cita';
                inputPersonalizado.required = true;
                inputPersonalizado.focus();
            });

            btnVolverBloques.addEventListener('click', function() {
                contenedorPersonalizado.classList.add('hidden');
                btnOtraHora.classList.remove('hidden');

                inputPersonalizado.removeAttribute('name');
                inputPersonalizado.required = false;
                inputPersonalizado.value = '';

                if (radiosBloque.length > 0) {
                    radiosBloque[0].required = true;
                }
            });

            radiosBloque.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        inputPersonalizado.removeAttribute('name');
                        inputPersonalizado.required = false;
                        inputPersonalizado.value = '';
                    }
                });
            });
        });
    </script>
@endsection
