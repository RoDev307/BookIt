<?php
namespace Tests\Feature\BookIt;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SharedLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_vue_login_preserves_session_contract_without_issuing_a_token(): void
    {
        $user = User::factory()->create(['role' => 'super_admin', 'password' => Hash::make('test-password')]);
        $this->withHeader('Origin', 'http://localhost:5173')->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'test-password',
        ])->assertOk()->assertJsonPath('user.id', $user->id)->assertJsonMissingPath('data.token');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->getJson('/api/v1/auth/user')->assertOk()->assertJsonPath('user.role', 'super_admin');
        $this->postJson('/api/v1/auth/logout')->assertOk();
        $this->assertGuest('web');
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/auth/user')->assertUnauthorized();
    }

    public function test_ionic_login_preserves_bearer_contract_and_revokes_its_token(): void
    {
        $user = User::factory()->create(['role' => 'admin_business', 'password' => Hash::make('test-password')]);
        $response = $this->withHeader('Origin', 'http://localhost:8100')->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'test-password',
        ])->assertOk()->assertJsonPath('data.user.id', $user->id);
        $this->assertIsString($response->json('data.token'));
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->withToken($response->json('data.token'))->postJson('/api/v1/auth/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_email_alone_does_not_grant_superadmin_permissions(): void
    {
        $user = User::factory()->create(['email' => 'admin@bookit.com', 'role' => 'client']);
        \Laravel\Sanctum\Sanctum::actingAs($user);
        $this->getJson('/api/v1/superadmin/businesses')->assertForbidden();
        $this->getJson('/api/v1/tenant/clients')->assertForbidden();
    }
}
