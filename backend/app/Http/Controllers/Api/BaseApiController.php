<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Retorna una respuesta JSON exitosa estandarizada.
     */
    public function sendResponse(mixed $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Retorna una respuesta JSON de error estandarizada.
     */
    public function sendError(string $error, array|string $errorMessages = [], int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = is_array($errorMessages) ? $errorMessages : [$errorMessages];
        }

        return response()->json($response, $code);
    }

    /**
     * Obtiene de forma segura el business_id del inquilino autenticado.
     */
    protected function getTenantBusinessId(): ?int
    {
        $user = auth()->user();
        return $user?->business_id;
    }
}
