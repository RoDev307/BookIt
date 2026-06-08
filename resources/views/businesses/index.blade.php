@extends('layouts.public')

@section('title', 'Nuestros Clientes Corporativos - BookIt')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="text-center max-w-3xl mx-auto mb-16">
            <span
                class="bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-indigo-100 dark:border-indigo-900/50">
                El sistema de citas que tu negocio necesita
            </span>
            <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight sm:text-5xl mt-4">
                Comercios Aliados de <span class="text-indigo-600 dark:text-indigo-400">BookIt</span>
            </h1>
            <p class="mt-4 text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                Conoce los establecimientos que optimizan sus flujos operativos, catálogos de servicios y calendarios en
                tiempo real a través de nuestra infraestructura distribuida.
            </p>
        </div>

        @if (session('success'))
            <div
                class="max-w-2xl mx-auto mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-400 rounded-xl text-center shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">

            @forelse($businesses as $business)
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col justify-between group transition-colors">
                    <div>
                        <div class="h-48 w-full overflow-hidden bg-slate-200 dark:bg-slate-700 relative">
                            @if ($business->image_url)
                                <img src="{{ $business->image_url }}" alt="{{ $business->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                @if (str_contains($business->slug, 'barberia') || str_contains($business->slug, 'olympus'))
                                    <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=600&auto=format&fit=crop"
                                        alt="Barbería" class="w-full h-full object-cover">
                                @elseif(str_contains($business->slug, 'clinica') ||
                                        str_contains($business->slug, 'sonrisas') ||
                                        str_contains($business->slug, 'dental'))
                                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?q=80&w=600&auto=format&fit=crop"
                                        alt="Clínica Dental" class="w-full h-full object-cover">
                                @else
                                    <img src="https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?q=80&w=600&auto=format&fit=crop"
                                        alt="Taller Mecánico" class="w-full h-full object-cover">
                                @endif
                            @endif
                            <div
                                class="absolute top-4 right-4 bg-emerald-500 text-white font-extrabold text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                Sistema Activo
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $business->name }}</h3>
                            <p class="mt-2 text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                                {{ $business->description ?? 'Establecimiento comercial con aislamiento multi-inquilino optimizado para la automatización de citas y control de personal.' }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700/50 flex justify-between items-center text-xs text-slate-400 dark:text-slate-400 font-medium">
                        <span>Licencia de Instancia:</span>
                        <span
                            class="text-indigo-600 dark:text-indigo-400 font-bold bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 px-2 py-0.5 rounded-md">Enterprise</span>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
                    <p class="text-slate-400 dark:text-slate-400 font-medium">No se encontraron comercios registrados en
                        esta instancia de la base de datos.</p>
                </div>
            @endforelse

        </div>
    </div>
@endsection
