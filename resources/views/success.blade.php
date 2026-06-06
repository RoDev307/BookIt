<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Confirmada - DATABOX</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans antialiased flex items-center justify-center min-h-screen">

    <div class="max-w-md w-full mx-4 bg-white rounded-3xl shadow-xl border border-slate-100 p-8 text-center">

        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-50 mb-6">
            <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-2xl font-black text-slate-900 tracking-tight">
            {{ session('success', '¡Reserva Completada!') }}
        </h1>
        <p class="text-sm text-slate-500 mt-2">Tu espacio ha sido bloqueado en la agenda del comercio. ¡Te esperamos!</p>

        <div class="mt-6 bg-slate-50 rounded-2xl p-5 text-left space-y-3 border border-slate-100">
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-medium">Servicio:</span>
                <span class="font-bold text-slate-800">{{ session('servicio', 'Servicio Seleccionado') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-medium">Fecha:</span>
                <span class="font-semibold text-slate-700">{{ session('fecha', 'Fecha de la cita') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-medium">Horario:</span>
                <span class="font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-md text-xs font-bold">
                    {{ session('hora', 'Hora seleccionada') }}
                </span>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('appointments.pdf', ['fecha' => session('fecha'), 'hora' => session('hora')]) }}" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm py-3.5 rounded-xl shadow-sm transition-all text-center block">
                Descargar Comprobante PDF
            </a>
        </div>

        <div class="mt-8">
            <a href="/negocios" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm py-3.5 rounded-xl shadow-sm transition-all text-center block">
                Volver al Catálogo
            </a>
        </div>
    </div>

</body>

</html>