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

            <!-- Nombre del Cliente Externo -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre del
                    Cliente</label>
                <input type="text" name="client_name" value="{{ old('client_name') }}" required
                    placeholder="Ej. Juan Pérez"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <!-- Servicio (Solo del negocio logueado) -->
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
            <!-- Fecha y Hora -->
            <!-- SECCIÓN CRONOLÓGICA DIVIDIDA -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- 1. Selección del Día -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">1. Selecciona la
                        Fecha</label>
                    <input type="date" name="fecha_cita" value="{{ old('fecha_cita') }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <!-- 2. Bloques Horarios Disponibles -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">2. Selecciona la
                        Hora</label>
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Bloque 08:00 AM -->
                        <label
                            class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="hora_cita" value="08:00" class="sr-only peer" required
                                {{ old('hora_cita') == '08:00' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">08:00 AM</span>
                        </label>

                        <!-- Bloque 09:30 AM -->
                        <label
                            class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="hora_cita" value="09:30" class="sr-only peer"
                                {{ old('hora_cita') == '09:30' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">09:30 AM</span>
                        </label>

                        <!-- Bloque 11:00 AM -->
                        <label
                            class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="hora_cita" value="11:00" class="sr-only peer"
                                {{ old('hora_cita') == '11:00' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">11:00 AM</span>
                        </label>

                        <!-- Bloque 01:00 PM -->
                        <label
                            class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="hora_cita" value="13:00" class="sr-only peer"
                                {{ old('hora_cita') == '13:00' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">01:00 PM</span>
                        </label>

                        <!-- Bloque 02:30 PM -->
                        <label
                            class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="hora_cita" value="14:30" class="sr-only peer"
                                {{ old('hora_cita') == '14:30' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">02:30 PM</span>
                        </label>

                        <!-- Bloque 04:00 PM -->
                        <label
                            class="border border-slate-200 rounded-xl p-3 text-center cursor-pointer hover:bg-slate-50 transition-all block has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="hora_cita" value="16:00" class="sr-only peer"
                                {{ old('hora_cita') == '16:00' ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700 peer-checked:text-indigo-600">04:00 PM</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Observaciones / Comentarios -->
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
@endsection
