@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Agregar Nuevo Servicio</h3>
            <p class="text-xs text-slate-500 mt-0.5">Completa la ficha técnica para publicar el servicio en el catálogo
                público de reservas.</p>
        </div>

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

        <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">ID del Comercio
                    Asociado</label>
                <input type="number" name="business_id" value="{{ old('business_id', 1) }}" required placeholder="Ej. 1"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre del
                    Servicio</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="Ej. Corte de Cabello Ejecutivo"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Descripción del
                    Servicio</label>
                <textarea name="description" rows="3"
                    placeholder="Detalla qué incluye el servicio y los beneficios para el cliente..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Precio Base
                        ($)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required
                        placeholder="0.00"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Duración Estimada
                        (Minutos)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 30) }}" required
                        placeholder="30"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>
            </div>

            <div class="pt-4 flex gap-3 border-t border-slate-100 mt-6">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">
                    Guardar Servicio
                </button>
                <a href="{{ route('services.index') }}"
                    class="border border-slate-200 text-slate-600 text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-all text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
