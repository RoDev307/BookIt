@extends('layouts.public')

@section('title', 'Catálogo de Comercios - BookIt')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl">
                Catálogo de <span class="text-indigo-600">Comercios</span>
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Selecciona un negocio para explorar sus servicios disponibles y gestionar tus reservas en tiempo real.
            </p>
        </div>

        @if (session('success'))
            <div
                class="max-w-2xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-center shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="h-48 w-full overflow-hidden bg-slate-200 relative">
            @if ($business->image_url)
                <img src="{{ $business->image_url }}" alt="{{ $business->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                @if (str_contains($business->slug, 'barberia') || str_contains($business->slug, 'olympus'))
                    <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=600&auto=format&fit=crop"
                        alt="Barbería"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @elseif(str_contains($business->slug, 'clinica') ||
                        str_contains($business->slug, 'sonrisas') ||
                        str_contains($business->slug, 'dental'))
                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?q=80&w=600&auto=format&fit=crop"
                        alt="Clínica Dental"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <img src="https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?q=80&w=600&auto=format&fit=crop"
                        alt="Taller Mecánico"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @endif
            @endif
        </div>
    </div>
@endsection
