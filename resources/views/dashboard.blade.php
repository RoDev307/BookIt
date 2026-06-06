<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Citas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">
                        Mis citas registradas
                    </h3>

                    @if(isset($appointments))

                        @forelse($appointments as $appointment)

                            <div class="border rounded p-4 mb-3">

                                <p>
                                    <strong>Fecha y hora:</strong>
                                    {{ $appointment->appointment_time }}
                                </p>

                                <p>
                                    <strong>Estado:</strong>
                                    {{ $appointment->status }}
                                </p>

                                <p>
                                    <strong>Notas:</strong>
                                    {{ $appointment->notes }}
                                </p>
                            <form action="{{ route('appointments.cancel', $appointment->id) }}"method="POST">

                                @csrf
                                @method('PATCH')

                                 <button type="submit"class="bg-red-500 text-white px-3 py-1 rounded">
                                    Cancelar cita
                                 </button>

                            </form>

                            </div>

                        @empty

                            <p>No tienes citas registradas.</p>

                        @endforelse

                    @else

                        <p>No se encontraron citas.</p>

                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>