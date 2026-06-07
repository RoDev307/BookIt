@extends('admin.layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto py-4">

        {{-- Navegación de Retorno y Títulos --}}
        <div class="mb-6">
            <a href="{{ route('master.businesses.index') }}"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                ← Volver al Listado Maestro de Inquilinos
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Dar de Alta Nuevo Comercio</h1>
            <p class="text-xs text-slate-500 mt-1">Registra una nueva instancia comercial en el clúster de Aiven y asígnale
                su credencial administrativa centralizada.</p>
        </div>

        {{-- Formulario unificado de Alta Centralizada --}}
        <form action="{{ route('master.businesses.store') }}" method="POST"
            class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            @csrf

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

            {{-- 📁 SECCIÓN 1: DATOS GENERALES DEL ESTABLECIMIENTO --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">1. Parámetros del Comercio</h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre Oficial del
                        Negocio</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium"
                        placeholder="Ej. Restaurante Premium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Descripción de la
                        Instancia</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium placeholder:font-normal"
                        placeholder="Breve reseña comercial explicativa del comercio...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL de Imagen
                        Representativa</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium text-xs font-mono"
                        placeholder="https://wtcsansalvador.com/...">
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- 👤 SECCIÓN 2: CREDENCIALES DEL OPERADOR / TENANT LOCAL --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-widest font-mono">2. Cuenta del
                    Administrador Local</h3>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nombre Completo del
                        Encargado</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium"
                        placeholder="Ej. Javier Siman">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Correo Electrónico
                        de Login Corporativo</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium text-xs font-mono"
                        placeholder="restaurante@bookit.com">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Contraseña Temporal
                        de Acceso</label>
                    {{-- 🚨 CORREGIDO: Añadido input explícito con name="admin_password" e id correspondiente --}}
                    <input type="password" name="admin_password" id="admin_password" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-800 font-medium"
                        placeholder="Digita una clave (Mínimo 8 caracteres)">
                </div>
            </div>

            {{-- Botón de Procesamiento Global --}}
            <div class="pt-4">
                <button type="submit"
                    class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-md shadow-indigo-600/10 transition-colors cursor-pointer uppercase tracking-wider">
                    🚀 Fundar Instancia y Activar Acceso Local
                </button>
            </div>
        </form>
    </div>
@endsection
