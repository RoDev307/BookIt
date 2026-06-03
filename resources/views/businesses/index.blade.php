<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Negocios - BookIt</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans antialiased">

    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <span class="text-2xl font-black text-indigo-600 tracking-tight">BookIt</span>
                <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                    <a href="/negocios" class="text-indigo-600 border-b-2 border-indigo-600 px-1 py-5">Inicio</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">Explorar</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">Mis Reservas</a>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button class="flex items-center space-x-2 text-sm font-semibold text-slate-700 hover:text-slate-900 transition-colors bg-slate-100 px-3 py-1.5 rounded-xl">
                    <div class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                        O
                    </div>
                    <span class="hidden sm:inline">Mi Cuenta</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight sm:text-5xl">
                Catálogo de <span class="text-indigo-600">Comercios</span>
            </h1>
            <p class="mt-4 text-lg text-slate-600">
                Selecciona un negocio para explorar sus servicios disponibles y gestionar tus reservas en tiempo real.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="h-48 w-full overflow-hidden bg-slate-200 relative">
                        <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=600&auto=format&fit=crop"
                            alt="Barbería"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900">Barbería Central</h3>
                        <p class="mt-2 text-slate-600 text-sm leading-relaxed">Estilo, cortes modernos y cuidado de barba con barberos profesionales altamente capacitados.</p>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <a href="/comercio/barberia-central" class="w-full text-center inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-sm">
                        Ver Servicios disponibles
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="h-48 w-full overflow-hidden bg-slate-200 relative">
                        <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?q=80&w=600&auto=format&fit=crop"
                            alt="Clínica Médica"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900">Clínica Médica Integral</h3>
                        <p class="mt-2 text-slate-600 text-sm leading-relaxed">Consultas generales y especializadas enfocadas en el cuidado prioritario y preventivo de tu salud.</p>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <a href="/comercio/clinica-medica-integral" class="w-full text-center inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-sm">
                        Ver Servicios disponibles
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="h-48 w-full overflow-hidden bg-slate-200 relative">
                        <img src="https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?q=80&w=600&auto=format&fit=crop"
                            alt="Taller Mecánico"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900">Taller AutoFix</h3>
                        <p class="mt-2 text-slate-600 text-sm leading-relaxed">Mantenimiento preventivo, diagnóstico computarizado y soluciones de mecánica general de alta precisión.</p>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100">
                    <a href="/comercio/taller-autofix" class="w-full text-center inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-sm">
                        Ver Servicios disponibles
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>

</html>