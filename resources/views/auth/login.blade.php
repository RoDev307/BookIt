@extends('layouts.guest')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Correo Electrónico</label>
            <input id="email"
                class="block mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5 bg-slate-50"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
            <input id="password"
                class="block mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2.5 bg-slate-50"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Recordar mi sesión</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4 border-t border-slate-100 pt-4">

            <div class="flex flex-col gap-1 text-center sm:text-left">
                @if (Route::has('register'))
                    <p class="text-sm text-slate-500">
                        ¿No tienes una cuenta?
                        <a class="font-semibold text-indigo-600 hover:text-indigo-700 underline rounded-md focus:outline-none"
                            href="{{ route('register') }}">
                            Regístrate aquí
                        </a>
                    </p>
                @endif

                @if (Route::has('password.request'))
                    <a class="text-xs text-slate-400 hover:text-indigo-600 underline rounded-md focus:outline-none"
                        href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer text-center">
                Iniciar Sesión
            </button>
        </div>
    </form>
@endsection
