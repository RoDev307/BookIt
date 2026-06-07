@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="pb-6 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Control de Citas Recibidas</h3>
            <p class="text-xs text-slate-500 mt-0.5">Aquí verás únicamente las reservas que corresponden a tu
                establecimiento.</p>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50">
                        <th class="p-4">ID Reserva</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Fecha y Hora</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Notas</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-bold text-slate-400">#{{ $appointment->id }}</td>
                            <td class="p-4 font-semibold text-slate-900">
                                {{ $appointment->user->name ?? 'Cliente Registrado' }}</td>
                            <td class="p-4 text-slate-600 font-medium">
                                📅 {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y - g:i A') }}
                            </td>
                            <td class="p-4">
                                @if ($appointment->status === 'confirmed')
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Confirmada</span>
                                @elseif($appointment->status === 'cancelled')
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Cancelada</span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Pendiente</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-slate-500 max-w-xs truncate">
                                {{ $appointment->notes ?? 'Sin comentarios.' }}</td>
                            <td class="p-4 text-right">
                                @if ($appointment->status !== 'cancelled')
                                    <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST"
                                        onsubmit="return confirm('¿Deseas cancelar esta reserva del cliente?')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-900 font-bold text-xs cursor-pointer">Cancelar
                                            Cita</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Ninguna acción</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-medium bg-slate-50/30">
                                No han registrado reservas para tu negocio todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
