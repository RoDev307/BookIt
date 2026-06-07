@extends('admin.layouts.app')

@section('content')
    <div class="max-w-xl mx-auto py-4">

        <div class="mb-6">
            <a href="{{ route('dashboard') }}"
                class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                ← Volver al Resumen Operativo
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Reprogramar Reserva #{{ $appointment->id }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">Modifica la asignación cronológica de la cita para el cliente.</p>
        </div>

        <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST"
            class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
                    <ul class="list-disc list-inside text-xs font-semibold text-rose-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🚨 CORREGIDO: Campo editable para corregir el nombre real de la persona --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre del
                    Cliente</label>
                <input type="text" name="client_name" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-bold"
                    value="{{ old('client_name', trim(explode('|', str_replace('Cliente Externo:', '', $appointment->notes))[0])) }}">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nueva Fecha y Hora
                    Seleccionada</label>
                <input type="datetime-local" name="appointment_time" required
                    value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d\TH:i')) }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notas / Observaciones
                    Adicionales</label>
                <textarea name="notes" rows="3"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium placeholder:font-normal"
                    placeholder="Ej. Solicitó mover la hora por inconvenientes de tráfico...">{{ old('notes', trim(explode('|', $appointment->notes)[1] ?? '')) }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-md shadow-indigo-600/10 transition-colors cursor-pointer uppercase tracking-wider">
                    💾 Guardar Cambios y Notificar Agenda
                </button>
            </div>
        </form>
    </div>
@endsection
