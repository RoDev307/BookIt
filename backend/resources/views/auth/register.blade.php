@extends('layouts.guest')

@section('content')
    <div>

        <!-- Botón para Regresar al Inicio Público -->
        <div class="mb-5 text-left">
            <a href="{{ route('businesses.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors group">
                <span class="transform group-hover:-translate-x-0.5 transition-transform">←</span> Regresar al Inicio
            </a>
        </div>

        <!-- Encabezado del Embudo de Conversión B2B -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Contratar Ecosistema BookIt</h2>
            <p class="text-xs text-slate-500 mt-1">Da de alta tu empresa y activa tu instancia administrativa para comenzar a
                gestionar servicios.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name (Representante) -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre
                    del Representante</label>
                <x-text-input id="name"
                    class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" type="text"
                    name="name" :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Ej. Lic. Alejandro Sosa" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-rose-600" />
            </div>

            <!-- Email Address -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico
                    Comercial</label>
                <x-text-input id="email"
                    class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" type="email"
                    name="email" :value="old('email')" required autocomplete="username" placeholder="ejemplo@empresa.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-600" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contraseña de
                    Seguridad</label>
                <x-text-input id="password"
                    class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" type="password"
                    name="password" required autocomplete="new-password" placeholder="Mínimo 8 caracteres" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-600" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirmar Contraseña de
                    Seguridad</label>
                <x-text-input id="password_confirmation"
                    class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" type="password"
                    name="password_confirmation" required autocomplete="new-password" placeholder="Repite tu contraseña" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-rose-600" />
            </div>

            <!-- Botón de Suscripción / Contratación -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-sm transition-colors cursor-pointer text-center block">
                    🚀 Activar Licencia e Iniciar Instancia
                </button>
            </div>

            <div class="text-center pt-4 border-t border-slate-100 mt-4 flex flex-col gap-1">
                <a class="text-xs text-slate-500 hover:text-indigo-600 transition-colors font-medium"
                    href="{{ route('login') }}">
                    ¿Ya posees una suscripción activa? <span class="text-indigo-600 font-bold underline">Inicia sesión
                        aquí</span>
                </a>
            </div>
        </form>
    </div>
@endsection
