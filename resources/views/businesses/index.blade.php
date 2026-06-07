@extends('layouts.public')

@section('title', 'Nuestros Clientes Corporativos - BookIt')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Encabezado con Enfoque SaaS Corporativo -->
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span
                class="bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-indigo-100">
                Infraestructura Multi-Tenant
            </span>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl mt-4">
                Comercios Aliados de Nuestra <span class="text-indigo-600">Red SaaS</span>
            </h1>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                Conoce los establecimientos e inquilinos corporativos que optimizan sus flujos operativos, catálogos de
                servicios y calendarios en tiempo real a través de nuestra infraestructura en la nube.
            </p>
        </div>

        @if (session('success'))
            <div
                class="max-w-2xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-center shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grilla de Casos de Éxito -->
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">

            @forelse($businesses as $business)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Contenedor Dinámico de Portadas -->
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
                            <!-- Etiqueta de Aislamiento de Datos -->
                            <div
                                class="absolute top-4 right-4 bg-emerald-500 text-white font-extrabold text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                                Caso de Éxito Activo
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                {{ $business->name }}</h3>
                            <p class="mt-2 text-slate-600 text-sm leading-relaxed">
                                {{ $business->description ?? 'Establecimiento comercial con aislamiento multi-inquilino optimizado para la automatización de citas y control de personal.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Botón de Inspección Técnica de Catálogo -->
                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex flex-col gap-3">
                        <div class="flex justify-between items-center text-xs text-slate-400 font-medium px-1">
                            <span>Estado de Licencia:</span>
                            <span
                                class="text-emerald-600 font-bold bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md">Operando</span>
                        </div>
                        <a href="{{ route('businesses.show', $business->slug) }}"
                            class="w-full text-center inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-sm cursor-pointer">
                            Ver Demostración de Catálogo
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-slate-300">
                    <p class="text-slate-400 font-medium">No se encontraron comercios registrados en esta instancia de la
                        base de datos.</p>
                </div>
            @endforelse

        </div>
    </div>
@endsection
