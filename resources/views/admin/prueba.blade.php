@extends('admin.layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
    <h3 class="text-2xl font-bold text-gray-800 mb-2">¡Bienvenido al Panel de Administración Interno!</h3>
    <p class="text-gray-600">
        Esta es la estructura base maquetada para el Sprint 2. Desde aquí, los administradores del sistema y los encargados de los negocios podrán gestionar las reservas, bloquear asuetos y configurar las disponibilidades horarias en tiempo real.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
            <span class="block text-sm text-indigo-500 uppercase font-semibold">Negocios Activos</span>
            <span class="text-3xl font-bold text-slate-800">3</span>
        </div>
        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100">
            <span class="block text-sm text-emerald-500 uppercase font-semibold">Citas para Hoy</span>
            <span class="text-3xl font-bold text-slate-800">0</span>
        </div>
        <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
            <span class="block text-sm text-amber-500 uppercase font-semibold">Próximos Asuetos</span>
            <span class="text-3xl font-bold text-slate-800">0</span>
        </div>
    </div>
</div>
@endsection
