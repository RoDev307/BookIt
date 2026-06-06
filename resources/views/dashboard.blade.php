@extends('layouts.app')

@section('header', 'Mis Citas')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50/70">
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Historial de Citas Registradas</h3>
            <p class="text-xs text-slate-500 mt-1">Controla tus estados de reserva y cancelaciones en tiempo real con la nube
                de Aiven.</p>
        </div>

        <div class="p-6">
            @if (isset($appointments))

                <div class="space-y-4">
                    @forelse($appointments as $appointment)
                        <div
                            class="border border-slate-200 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-slate-50/40 transition-colors gap-4">

                            <div class="space-y-2">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Reserva
                                        #{{ $appointment->id }}</span>

                                    @if ($appointment->status === 'confirmed')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">✓
                                            Confirmada</span>
                                    @elseif($appointment->status === 'cancelled')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">✕
                                            Cancelada</span>
                                    @elseif($appointment->status === 'completed')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">🏁
                                            Finalizada</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">⏳
                                            Pendiente</span>
                                    @endif
                                </div>

                                <p class="text-slate-800 font-semibold text-base flex items-center gap-2">
                                    📅 <span
                                        class="text-slate-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y - g:i A') }}</span>
                                </p>

                                @if ($appointment->notes)
                                    <p
                                        class="text-xs text-slate-600 bg-slate-100 p-2.5 rounded-xl border border-slate-200 max-w-xl">
                                        <strong class="text-slate-700 block mb-0.5">Notas dejadas:</strong>
                                        {{ $appointment->notes }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex sm:flex-col items-stretch gap-2 w-full sm:w-auto">
                                @if ($appointment->status !== 'cancelled')
                                    <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST"
                                        onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta cita de forma definitiva?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full text-center bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">
                                            Cancelar Cita
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-12 border border-dashed border-slate-300 rounded-2xl">
                            <p class="text-slate-400 font-medium">No tienes citas agendadas actualmente en el sistema.</p>
                            <a href="{{ route('businesses.index') }}"
                                class="inline-block mt-4 text-sm font-bold text-indigo-600 hover:underline">Explorar
                                comercios para agendar →</a>
                        </div>
                    @endforelse
                </div>
            @else
                <p class="text-slate-400 text-center py-6 font-medium">No se encontraron registros de citas vinculados.</p>
            @endif
        </div>
    </div>
@endsection
