@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-slate-100 gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Servicios Ofertados</h3>
                <p class="text-xs text-slate-500 mt-0.5">Gestión de portafolio comercial y sincronización con el motor de
                    citas.</p>
            </div>
            <a href="{{ route('services.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">
                + Nuevo Servicio
            </a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50">
                        <th class="p-4">Título del Servicio</th>
                        <th class="p-4">Comercio</th>
                        <th class="p-4">Duración</th>
                        <th class="p-4">Precio Base</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($services as $service)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-semibold text-slate-900">{{ $service->name }}</td>
                            <td class="p-4"><span
                                    class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2 py-1 rounded-md">ID:
                                    {{ $service->business_id }}</span></td>
                            <td class="p-4 text-slate-500">⏱️ {{ $service->duration_minutes }} min</td>
                            <td class="p-4 font-bold text-slate-900">${{ number_format($service->price, 2) }}</td>
                            <td class="p-4 text-right flex justify-end gap-3">
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Editar</a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar este servicio del catálogo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-rose-600 hover:text-rose-900 font-semibold text-xs cursor-pointer">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 font-medium bg-slate-50/30">
                                No hay servicios registrados en este momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
