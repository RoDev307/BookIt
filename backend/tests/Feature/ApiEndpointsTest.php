<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_list_businesses_publicly(): void
    {
        $response = $this->getJson('/api/v1/businesses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'slug']
                ]
            ]);
    }

    public function test_can_register_new_client_via_api(): void
    {
        $uniqueEmail = 'client_' . time() . '@test.com';

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Cliente API',
            'email'                 => $uniqueEmail,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ]
            ]);
    }

    public function test_can_login_via_api_and_get_token(): void
    {
        $uniqueEmail = 'user_' . time() . '@test.com';
        User::create([
            'name'     => 'Usuario Login',
            'email'    => $uniqueEmail,
            'password' => Hash::make('secret1234'),
            'role'     => 'client',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $uniqueEmail,
            'password' => 'secret1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'token', 'token_type']
            ]);
    }

    public function test_can_check_business_availability(): void
    {
        $business = Business::create([
            'name' => 'Barbería Test',
            'slug' => 'barberia-test-' . time(),
            'email' => 'barberia@test.com',
        ]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->getJson("/api/v1/businesses/{$business->slug}/availability?date={$nextMonday}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'date',
                    'is_open',
                    'available_slots' => [
                        '*' => ['time', 'available', 'reason']
                    ]
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_tenant_dashboard(): void
    {
        $response = $this->getJson('/api/v1/tenant/dashboard');

        $response->assertStatus(401);
    }

    public function test_authenticated_client_can_book_and_list_appointments(): void
    {
        $business = Business::create([
            'name'  => 'Spa Relax',
            'slug'  => 'spa-relax-' . time(),
            'email' => 'spa@test.com',
        ]);

        $service = Service::create([
            'business_id'      => $business->id,
            'name'             => 'Masaje Relajante',
            'duration_minutes' => 60,
            'price'            => 35.00,
            'is_active'        => true,
        ]);

        $client = User::create([
            'name'     => 'Cliente Registrado',
            'email'    => 'client_book_' . time() . '@test.com',
            'password' => Hash::make('password123'),
            'role'     => 'client',
        ]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->setTime(10, 0, 0)->format('Y-m-d H:i:s');

        // Reservar cita
        $bookingResponse = $this->actingAs($client, 'sanctum')->postJson('/api/v1/appointments', [
            'service_id'       => $service->id,
            'appointment_time' => $nextMonday,
            'notes'            => 'Primera sesión',
        ]);

        $bookingResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'confirmed');

        $appointmentId = $bookingResponse->json('data.id');

        // Listar mis citas
        $listResponse = $this->actingAs($client, 'sanctum')->getJson('/api/v1/my-appointments');
        $listResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // Cancelar cita
        $cancelResponse = $this->actingAs($client, 'sanctum')->patchJson("/api/v1/appointments/{$appointmentId}/cancel");
        $cancelResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_tenant_admin_can_manage_services(): void
    {
        $business = Business::create([
            'name'  => 'Clínica Dental',
            'slug'  => 'clinica-dental-' . time(),
            'email' => 'dental@test.com',
        ]);

        $admin = User::create([
            'name'        => 'Admin Dental',
            'email'       => 'admin_dental_' . time() . '@test.com',
            'password'    => Hash::make('password123'),
            'role'        => 'admin_business',
            'business_id' => $business->id,
        ]);

        // Crear servicio
        $createResponse = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/tenant/services', [
            'name'             => 'Limpieza Dental Pro',
            'price'            => 25.00,
            'duration_minutes' => 45,
            'description'      => 'Limpieza con ultrasonido',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Limpieza Dental Pro');

        $serviceId = $createResponse->json('data.id');

        // Actualizar servicio
        $updateResponse = $this->actingAs($admin, 'sanctum')->putJson("/api/v1/tenant/services/{$serviceId}", [
            'name'             => 'Limpieza Dental Avanzada',
            'price'            => 30.00,
            'duration_minutes' => 50,
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Limpieza Dental Avanzada');

        // Listar servicios de tenant
        $listResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/tenant/services');
        $listResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_superadmin_can_access_dashboard_and_create_business(): void
    {
        $superAdmin = User::create([
            'name'     => 'Root Maestro',
            'email'    => 'admin@bookit.com',
            'password' => Hash::make('itca123#'),
            'role'     => 'super_admin',
        ]);

        $dashResponse = $this->actingAs($superAdmin, 'sanctum')->getJson('/api/v1/superadmin/dashboard');
        $dashResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'metrics' => ['total_businesses', 'total_appointments', 'total_users']
                ]
            ]);

        // Crear comercio e inquilino
        $createResponse = $this->actingAs($superAdmin, 'sanctum')->postJson('/api/v1/superadmin/businesses', [
            'business_name'  => 'AutoFix Taller',
            'description'    => 'Taller mecánico general',
            'admin_name'     => 'Gerente Taller',
            'admin_email'    => 'taller_mgr_' . time() . '@test.com',
            'admin_password' => 'secretPass123',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.business.name', 'AutoFix Taller');
    }
}

