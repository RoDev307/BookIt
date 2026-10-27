<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends BaseApiController
{
    /**
     * Catálogo público de todos los comercios registrados.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Business::withCount(['services' => function ($q) {
            $q->where('is_active', true);
        }]);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $businesses = $query->orderBy('name', 'asc')->get();

        return $this->sendResponse($businesses);
    }

    /**
     * Detalle de un comercio específico por su slug (incluyendo servicios y horarios).
     */
    public function show(string $slug): JsonResponse
    {
        $business = Business::where('slug', $slug)
            ->with([
                'services' => function ($q) {
                    $q->where('is_active', true);
                },
                'hours' => function ($q) {
                    $q->orderBy('day_of_week', 'asc');
                }
            ])
            ->first();

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        return $this->sendResponse($business);
    }

    /**
     * Lista de servicios activos del comercio.
     */
    public function services(string $slug): JsonResponse
    {
        $business = Business::where('slug', $slug)->first();

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $services = $business->services()->where('is_active', true)->get();

        return $this->sendResponse($services);
    }

    /**
     * Horarios de atención configurados para el comercio.
     */
    public function hours(string $slug): JsonResponse
    {
        $business = Business::where('slug', $slug)->first();

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $hours = $business->hours()->orderBy('day_of_week', 'asc')->get();

        return $this->sendResponse($hours);
    }

    /**
     * Disponibilidad de horarios para una fecha específica (calcula slots libres).
     */
    public function availability(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'date'       => ['required', 'date_format:Y-m-d'],
            'staff_name' => ['nullable', 'string'],
        ]);

        $business = Business::where('slug', $slug)->first();

        if (!$business) {
            return $this->sendError('Comercio no encontrado.', [], 404);
        }

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek; // 0 = Domingo, 6 = Sábado

        // Buscar horario configurado
        $businessHour = BusinessHour::where('business_id', $business->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        $isClosed = false;
        $openHour = 8;
        $closeHour = 17;

        if ($businessHour) {
            if ($businessHour->is_closed) {
                $isClosed = true;
            } else {
                $openHour = (int) substr($businessHour->opening_time ?? '08:00', 0, 2);
                $closeHour = (int) substr($businessHour->closing_time ?? '17:00', 0, 2);
            }
        } else {
            // Regla por defecto: cerrado en fines de semana
            if ($date->isWeekend()) {
                $isClosed = true;
            }
        }

        if ($isClosed) {
            return $this->sendResponse([
                'date'             => $request->date,
                'is_open'          => false,
                'reason'           => 'El establecimiento se encuentra cerrado este día.',
                'available_slots'  => [],
            ]);
        }

        // Consultar citas existentes confirmadas en esa fecha
        $citasQuery = Appointment::where('business_id', $business->id)
            ->whereDate('appointment_time', $request->date)
            ->where('status', 'confirmed');

        if ($request->filled('staff_name') && $request->staff_name !== 'Asignado por Recepción') {
            $citasQuery->where('staff_name', $request->staff_name);
        }

        $ocupadas = $citasQuery->pluck('appointment_time')->map(function ($time) {
            return Carbon::parse($time)->format('H:i');
        })->toArray();

        // Generar intervalos de 1 hora
        $slots = [];
        $now = Carbon::now();

        for ($h = $openHour; $h < $closeHour; $h++) {
            $slotTime = sprintf('%02d:00', $h);
            $slotDateTime = Carbon::parse("{$request->date} {$slotTime}");

            $isPast = $slotDateTime->isPast();
            $isBooked = in_array($slotTime, $ocupadas, true);

            $slots[] = [
                'time'      => $slotTime,
                'available' => !$isPast && !$isBooked,
                'reason'    => $isPast ? 'Horario transcurrido' : ($isBooked ? 'Horario ocupado' : 'Disponible'),
            ];
        }

        return $this->sendResponse([
            'date'            => $request->date,
            'is_open'         => true,
            'business'        => [
                'id'   => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'available_slots' => $slots,
        ]);
    }
}
