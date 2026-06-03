<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookIt - Catálogo de Negocios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl x-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <span class="text-2xl font-bold text-indigo-600 tracking-tight">BookIt</span>
            <span class="text-sm text-gray-500 font-medium">Reservas en tiempo real</span>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                Encuentra tu próximo servicio
            </h1>
            <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto">
                Explora los negocios locales disponibles y reserva tu cita en segundos.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
            @forelse($businesses as $business)
                <div
                    class="group relative bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-between">
                    <div>
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-between text-indigo-600 font-bold text-lg mb-4">
                            <span class="w-full text-center">{{ strtoupper(substr($business->name, 0, 2)) }}</span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $business->name }}
                        </h3>

                        <p class="mt-2 text-sm text-gray-500">
                            {{ $business->email ?? 'Sin correo de contacto' }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <a href="#"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ver Servicios
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No hay negocios registrados en este momento.</p>
                </div>
            @endforelse
        </div>
    </main>

</body>

</html>
