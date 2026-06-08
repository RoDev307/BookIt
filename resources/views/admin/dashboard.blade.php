{{-- Determina dinámicamente el layout para no romper la UI horizontal de los clientes --}}
@extends(Auth::user()->role === 'client' ? 'layouts.app' : 'admin.layouts.app')

@if (Auth::user()->role === 'client')
    @section('header', 'Mis Citas')
@endif

@section('content')
    <div class="max-w-6xl mx-auto py-2">

        {{-- 1. ENTORNO ADMINISTRADOR MAESTRO (SIN NEGOCIO) --}}
        @if (is_null(Auth::user()->business_id) && Auth::user()->role !== 'client')
            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Consola de Infraestructura SaaS</h1>
                <p class="text-xs text-slate-500 mt-1">Monitoreo de actividad multi-tenant y estadísticas operativas del
                    clúster.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Comercios Afiliados</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalComercios ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-lg">
                        🌐</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Reservas Globales</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalCitas ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center text-lg">
                        📅</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Citas Confirmadas</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $citasConfirmadas ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center text-lg">
                        ✓</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Citas Canceladas</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $citasCanceladas ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-center text-lg">
                        ✕</div>
                </div>
            </div>

            {{-- Tabla Global --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Últimas Reservas en la Red</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Auditoría en tiempo real de transacciones entrantes.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr
                                class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/20">
                                <th class="p-4">ID</th>
                                <th class="p-4">Establecimiento</th>
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Fecha / Hora</th>
                                <th class="p-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @foreach ($appointments as $app)
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="p-4 text-slate-400">#{{ $app->id }}</td>
                                    <td class="p-4 font-bold text-slate-900">{{ $app->business->name ?? 'N/A' }}</td>

                                    {{-- Extractor con prioridad al campo editado --}}
                                    <td class="p-4 font-medium text-slate-600">
                                        @if (!empty($app->client_name))
                                            {{ $app->client_name }}
                                        @elseif (str_contains($app->notes ?? '', 'Cliente Externo:'))
                                            {{ trim(explode('|', str_replace('Cliente Externo:', '', $app->notes))[0]) }}
                                        @elseif (!empty($app->user->name) && $app->user->role === 'client')
                                            {{ $app->user->name }}
                                        @else
                                            <span class="text-slate-400 italic">Cliente Externo</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-slate-500 font-mono">
                                        {{ \Carbon\Carbon::parse($app->appointment_time)->format('d/m/Y - g:i A') }}</td>
                                    <td class="p-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $app->status === 'confirmed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                                            {{ $app->status === 'confirmed' ? 'Activa' : 'Cancelada' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. ENTORNO ADMINISTRADOR DE COMERCIO (TENANT LOCAL) -> MÉTRICAS DE SU LOCAL --}}
        @elseif(Auth::user()->role === 'admin_business')
            <div class="mb-6">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Resumen Operativo del Establecimiento</h1>
                <p class="text-xs text-slate-500 mt-1">Estadísticas y control del flujo de reservas asignadas a tu local.
                </p>
            </div>

            {{-- Métricas Locales del Comercio --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Citas Recibidas</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalCitas ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-lg">
                        📊</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Citas Confirmadas</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $citasConfirmadas ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center text-lg">
                        ✓</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Citas Canceladas</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1">{{ $citasCanceladas ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-center text-lg">
                        ✕</div>
                </div>
            </div>

            {{-- Próximas Citas Programadas del Local --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Próximas Reservas a Atender</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Control cronológico interno de la agenda de hoy.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr
                                class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/20">
                                <th class="p-4">ID</th>
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Fecha y Hora Programada</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse($appointments as $app)
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="p-4 text-slate-400">#{{ $app->id }}</td>
                                    <td class="p-4 font-bold text-slate-900">
                                        @if (str_contains($app->notes ?? '', 'Cliente Externo:'))
                                            {{ trim(explode('|', str_replace('Cliente Externo:', '', $app->notes))[0]) }}
                                        @elseif (!empty($app->user->name) && $app->user->role === 'client')
                                            {{ $app->user->name }}
                                        @else
                                            <span class="text-slate-400 font-normal italic">Cliente Externo (Manual)</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-slate-600 font-mono">
                                        {{ \Carbon\Carbon::parse($app->appointment_time)->format('d/m/Y - g:i A') }}</td>
                                    <td class="p-4">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $app->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                            {{ $app->status === 'confirmed' ? 'Confirmada' : 'Cancelada' }}
                                        </span>
                                    </td>

                                    <td class="p-4 text-right flex items-center justify-end gap-2">
                                        @if ($app->status !== 'cancelled')
                                            <a href="{{ route('admin.appointments.edit', $app->id) }}"
                                                class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] px-3 py-1.5 rounded-xl transition-colors">
                                                Editar
                                            </a>

                                            <form action="{{ route('appointments.cancel', $app->id) }}" method="POST"
                                                onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta cita?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 font-bold text-[11px] px-3 py-1.5 rounded-xl transition-colors cursor-pointer">
                                                    Cancelar Cita
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-400 text-xs font-normal italic">Sin acciones</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 font-normal">No tienes citas
                                        agendadas registradas en tu calendario de negocio.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. ENTORNO CLIENTE COMÚN (HISTORIAL TRADICIONAL) --}}
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200 bg-slate-50/70">
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight">Historial de Citas Registradas</h3>
                    <p class="text-xs text-slate-500 mt-1">Controla tus estados de reserva y cancelaciones en tiempo real
                        con la nube de Aiven.</p>
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
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $appointment->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                {{ $appointment->status === 'confirmed' ? '✓ Confirmada' : '✕ Cancelada' }}
                                            </span>
                                        </div>
                                        <p class="text-slate-800 font-semibold text-base flex items-center gap-2">
                                            📅 <span
                                                class="text-slate-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y - g:i A') }}</span>
                                        </p>
                                    </div>
                                    <div class="flex sm:flex-col items-stretch gap-2 w-full sm:w-auto">
                                        @if ($appointment->status !== 'cancelled')
                                            <form action="{{ route('appointments.cancel', $appointment->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta cita?')">
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
                                    <p class="text-slate-400 font-medium">No tienes citas agendadas actualmente en el
                                        sistema.</p>
                                    <a href="{{ route('businesses.index') }}"
                                        class="inline-block mt-4 text-sm font-bold text-indigo-600 hover:underline">Explorar
                                        comercios para agendar →</a>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
