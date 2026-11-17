<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\BusinessNotification;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_are_recorded_and_scoped_to_the_business(): void
    {
        $a = Business::create(['name' => 'A', 'slug' => 'a']);
        $b = Business::create(['name' => 'B', 'slug' => 'b']);
        $admin = User::factory()->create(['role' => 'admin_business', 'business_id' => $a->id]);
        $client = User::factory()->create(['role' => 'client']);
        $service = Service::create([
            'business_id' => $a->id,
            'name' => 'Corte',
            'price' => 10,
            'duration_minutes' => 30,
        ]);
        $appointment = Appointment::create([
            'user_id' => $client->id,
            'business_id' => $a->id,
            'service_id' => $service->id,
            'appointment_time' => '2027-01-01 10:00:00',
            'status' => 'confirmed',
        ]);
        $appointment->update(['status' => 'cancelled']);
        BusinessNotification::create([
            'business_id' => $b->id,
            'type' => 'created',
            'title' => 'Otra',
            'message' => 'Privada',
        ]);

        $this->actingAs($admin)->getJson('/api/v1/tenant/notifications')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissing(['title' => 'Otra']);

        $notification = BusinessNotification::where('business_id', $a->id)
            ->where('type', 'cancelled')
            ->firstOrFail();
        $this->actingAs($admin)
            ->patchJson('/api/v1/tenant/notifications/' . $notification->id . '/read')
            ->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);

        $other = BusinessNotification::where('business_id', $b->id)->firstOrFail();
        $this->actingAs($admin)
            ->patchJson('/api/v1/tenant/notifications/' . $other->id . '/read')
            ->assertNotFound();
    }

    public function test_profile_updates_email_without_changing_slug(): void
    {
        $business = Business::create(['name' => 'Negocio', 'slug' => 'negocio']);
        $admin = User::factory()->create(['role' => 'admin_business', 'business_id' => $business->id]);

        $this->actingAs($admin)->putJson('/api/v1/tenant/business', [
            'name' => 'Nuevo nombre',
            'email' => 'contacto@example.com',
            'description' => 'Descripción',
            'image_url' => null,
        ])->assertOk();

        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'name' => 'Nuevo nombre',
            'slug' => 'negocio',
            'email' => 'contacto@example.com',
        ]);
    }
}
