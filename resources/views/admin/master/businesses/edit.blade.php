@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="mb-6">
            <a href="{{ route('master.businesses.index') }}"
                class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">← Volver al listado
                maestro</a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Modificar Instancia: {{ $business->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">Estás alterando la información de este comercio como Administrador Maestro
                del sistema.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <form method="POST" action="{{ route('master.businesses.update', $business->id) }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                {{-- Alertas de validación de Laravel --}}
                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
                        <ul class="list-disc list-inside text-xs font-semibold text-rose-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">1. Parámetros del Comercio</h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre Oficial del
                        Negocio</label>
                    <input type="text" name="name" value="{{ old('name', $business->name) }}" required
                        class="block w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-slate-800 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Descripción del
                        negocio</label>
                    <textarea name="description" rows="4"
                        class="block w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-slate-800 font-medium">{{ old('description', $business->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL de Imagen
                        Representativa</label>
                    <input type="url" name="image_url" value="{{ old('image_url', $business->image_url) }}"
                        class="block w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-slate-800 font-medium font-mono text-xs">
                </div>

                <hr class="border-slate-100 my-4">

                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest font-mono mb-2">2. Identidad del
                    Administrador</h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre en Pantalla
                        (Esquina Inferior Izquierda)</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name', $business->owner->name ?? '') }}"
                        required
                        class="block w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 text-slate-800 font-semibold"
                        placeholder="Ej. Admin Clínica Sonrisas o Javier Siman">
                    <p class="text-[10px] text-slate-400 mt-1 font-medium">Este es el parámetro exacto que define la firma
                        que aparece abajo en la barra lateral oscura.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer uppercase tracking-wider">
                        Aplicar Modificación Global
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
