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
                    placeholder="Ej. Juan Pérez (Vía WhatsApp)"
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

            <!-- Fecha y Hora -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Fecha y Hora de la
                    Reserva</label>
                <input type="datetime-local" name="appointment_time" value="{{ old('appointment_time') }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
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
