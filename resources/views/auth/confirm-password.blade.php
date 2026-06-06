@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-sm text-slate-600 leading-relaxed">
        Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition-colors cursor-pointer">
                Confirmar
            </button>
        </div>
    </form>
@endsection
