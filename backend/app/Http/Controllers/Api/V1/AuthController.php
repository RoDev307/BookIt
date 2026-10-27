<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * Registro de nuevo usuario (por defecto rol cliente).
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'client',
            'business_id' => null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user'  => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->role,
                'business_id' => $user->business_id,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Usuario registrado exitosamente.', 201);
    }

    /**
     * Inicio de sesión y generación de Bearer Token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Credenciales incorrectas.', [
                'email' => ['El correo o la contraseña no coinciden con nuestros registros.']
            ], 422);
        }

        // Cargar negocio si es administrador de comercio
        if ($user->business_id) {
            $user->load('business');
        }

        // Crear token de Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse([
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->role,
                'business_id' => $user->business_id,
                'business'    => $user->business,
            ],
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 'Inicio de sesión exitoso.');
    }

    /**
     * Obtener el perfil del usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('business');

        return $this->sendResponse([
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->role,
                'business_id' => $user->business_id,
                'business'    => $user->business,
                'created_at'  => $user->created_at,
            ],
        ]);
    }

    /**
     * Actualizar perfil del usuario autenticado.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password'      => ['nullable', 'required_with:password', 'string'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
                return $this->sendError('La contraseña actual es incorrecta.', [
                    'current_password' => ['La contraseña actual proporcionada no coincide.']
                ], 422);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return $this->sendResponse([
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->role,
                'business_id' => $user->business_id,
            ]
        ], 'Perfil actualizado exitosamente.');
    }

    /**
     * Cierre de sesión (revoca el token actual).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'Sesión cerrada exitosamente.');
    }
}
