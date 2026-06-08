@extends('admin.layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-2">

        {{-- Encabezado de la página --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <a href="{{ route('dashboard') }}"
                    class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                    ← Volver al Resumen Operativo
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Agenda en Formato Calendario</h1>
                <p class="text-xs text-slate-500 mt-1">Planificación cronológica de citas y asignación de colaboradores en
                    tiempo real.</p>
            </div>
            <div>
                <a href="{{ route('admin.appointments.create') }}"
                    class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-indigo-600/10 transition-colors uppercase tracking-wider">
                    ➕ Agendar Cita Manual
                </a>
            </div>
        </div>

        {{-- Contenedor del Calendario --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div id="calendar" class="min-h-[650px]"></div>
        </div>
    </div>

    <div id="eventModal"
        class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div
            class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden transform scale-95 transition-all duration-200">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900 tracking-tight uppercase tracking-wider">Detalles de la Cita
                </h3>
                <button onclick="closeModal()"
                    class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cliente</span>
                    <p id="modalClient" class="text-sm font-bold text-slate-800 mt-0.5"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Colaborador</span>
                        <p id="modalStaff" class="text-xs font-semibold text-slate-700 mt-0.5"></p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Horario de
                            Inicio</span>
                        <p id="modalTime" class="text-xs font-mono text-indigo-600 font-bold mt-0.5"></p>
                    </div>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Notas y
                        Observaciones</span>
                    <p id="modalNotes"
                        class="text-xs text-slate-600 bg-slate-50 border border-slate-100 p-3 rounded-xl mt-1 italic"></p>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50/70 flex justify-end gap-2">
                <a id="modalEditBtn" href="#"
                    class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs px-4 py-2 rounded-xl transition-colors">
                    Editar Registro
                </a>
                <button onclick="closeModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition-colors cursor-pointer">
                    Entendido
                </button>
            </div>
        </div>
    </div>

    {{-- Estilos y Scripts de FullCalendar desde CDN Oficial --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/es.global.min.js"></script>

    <style>
        /* Personalización para acoplar FullCalendar con el diseño UI Minimalista del proyecto */
        .fc {
            --fc-border-color: #f1f5f9;
            --fc-button-bg-color: #ffffff;
            --fc-button-border-color: #e2e8f0;
            --fc-button-text-color: #334155;
            --fc-button-hover-bg-color: #f8fafc;
            --fc-button-active-bg-color: #f1f5f9;
            font-family: inherit;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem !important;
            font-weight: 900 !important;
            color: #0f172a;
            text-transform: capitalize;
        }

        .fc .fc-button {
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            border-radius: 10px !important;
            padding: 6px 12px !important;
            box-shadow: none !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: #ffffff !important;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b;
            text-transform: uppercase;
            padding: 8px 0 !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e2e8f0 !important;
        }

        .fc-event {
            border-radius: 8px !important;
            padding: 2px 6px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: "{{ route('admin.appointments.api') }}", // Captura el JSON del controlador
                firstDay: 1, // Lunes como primer día de la semana
                eventClick: function(info) {
                    // Inyectar datos al modal al hacer clic en una cita
                    document.getElementById('modalClient').innerText = info.event.title.split('(')[0]
                        .trim();
                    document.getElementById('modalStaff').innerText = info.event.extendedProps.staff;

                    // Formatear la fecha legible
                    const dateOptions = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    document.getElementById('modalTime').innerText = new Date(info.event.start)
                        .toLocaleDateString('es-SV', dateOptions);

                    document.getElementById('modalNotes').innerText = info.event.extendedProps.notes ||
                        'Sin observaciones registradas.';

                    // Configurar dinámicamente la ruta de edición con el ID de la cita
                    document.getElementById('modalEditBtn').href =
                        `/admin/appointments/${info.event.id}/edit`;

                    // Mostrar el modal con animación
                    const modal = document.getElementById('eventModal');
                    modal.classList.remove('hidden');
                }
            });

            calendar.render();
        });

        function closeModal() {
            document.getElementById('eventModal').classList.add('hidden');
        }
    </script>
@endsection
