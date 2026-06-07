@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Editar Servicio Comercial</h3>
            <p class="text-xs text-slate-500 mt-0.5">Modifica los valores técnicos del servicio seleccionado. Los cambios se
                actualizarán instantáneamente en el motor de citas.</p>
        </div>

        <!-- Mapeo de errores de validación de Laravel -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-xl">
                <strong class="font-bold block mb-1">Por favor corrige los siguientes campos:</strong>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('services.update', $service->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre del
                    Servicio</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">{{ old('description', $service->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Precio ($)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $service->price) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Duración
                        (Minutos)</label>
                    <input type="number" name="duration_minutes"
                        value="{{ old('duration_minutes', $service->duration_minutes) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>
            </div>

            <div class="pt-4 flex gap-3 border-t border-slate-100 mt-6">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">
                    Actualizar Servicio
                </button>
                <a href="{{ route('services.index') }}"
                    class="border border-slate-200 text-slate-600 text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
