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

        <!-- Encabezado de Sesión -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Consola de Acceso</h2>
            <p class="text-xs text-slate-500 mt-1">Introduce tus credenciales administrativas asignadas para gestionar tu
                comercio.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo
                    Electrónico</label>
                <x-text-input id="email"
                    class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" type="email"
                    name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="ejemplo@empresa.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-rose-600" />
            </div>

            <!-- Password -->
            <div>
                <label for="password"
                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contraseña de
                    Seguridad</label>
                <x-text-input id="password"
                    class="block mt-1 w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" type="password"
                    name="password" required autocomplete="current-password" placeholder="Ingresa tu clave" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-600" />
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-xs text-slate-500 font-medium">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Botón de Ingreso -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-sm transition-colors cursor-pointer text-center block">
                    🔑 Entrar al Panel de Control
                </button>
            </div>

            <div class="text-center pt-4 border-t border-slate-100 mt-4 flex flex-col gap-1">
                @if (Route::has('password.request'))
                    <a class="text-xs text-slate-400 hover:text-indigo-600 transition-colors font-medium mb-1"
                        href="{{ route('password.request') }}">
                        ¿Olvidaste tus datos de suscripción?
                    </a>
                @endif
                <a class="text-xs text-indigo-600 font-bold hover:underline" href="{{ route('register') }}">
                    ¿Tu negocio no está registrado? Regístrate aquí
                </a>
            </div>
        </form>
    </div>
@endsection
