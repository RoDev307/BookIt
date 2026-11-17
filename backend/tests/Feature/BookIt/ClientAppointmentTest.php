<?php
namespace Tests\Feature\BookIt;
use App\Models\{Appointment, Business, Client, Service, User};
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class ClientAppointmentTest extends TestCase
{
    use RefreshDatabase;
    private Business $business;
    private User $admin;
    private Service $service;
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite',config('database.default'));
        $this->assertSame(':memory:',config('database.connections.sqlite.database'));
        Carbon::setTestNow(Carbon::parse('2030-01-07 07:00:00','America/El_Salvador'));
        $this->business=Business::create(['name'=>'Clínica de prueba','slug'=>'clinica-test']);
        $this->admin=User::factory()->create(['role'=>'admin_business','business_id'=>$this->business->id]);
        $this->service=Service::create(['business_id'=>$this->business->id,'name'=>'Consulta','duration_minutes'=>30,'price'=>25,'is_active'=>true]);
    }
    protected function tearDown(): void { Carbon::setTestNow(); parent::tearDown(); }
    private function booking(array $extra=[]): array
    {
        return array_merge(['service_id'=>$this->service->id,'staff_name'=>'Profesional de prueba','appointment_time'=>'2030-01-07 10:00:00'],$extra);
    }
    private function client(): Client
    {
        return Client::create(['business_id'=>$this->business->id,'name'=>'Pedro Pérez','phone'=>'70001234']);
    }
    public function test_f01_registra_cuenta_client_sin_asignarle_negocio(): void
    {
        $r=$this->postJson('/api/v1/auth/register',['name'=>'Ana','email'=>'ana@example.test','password'=>'test-password','password_confirmation'=>'test-password']);
        $r->assertCreated()->assertJsonPath('data.user.role','client')->assertJsonPath('data.user.business_id',null);
        $this->assertDatabaseHas('users',['email'=>'ana@example.test','role'=>'client']);
    }
    public function test_f02_rechaza_credenciales_incorrectas(): void
    {
        $this->postJson('/api/v1/auth/login',['email'=>$this->admin->email,'password'=>'incorrecta'])->assertStatus(422);
        $this->assertDatabaseCount('personal_access_tokens',0);
    }
    public function test_f03_sin_sesion_no_consulta_clientes(): void
    {
        $this->getJson('/api/v1/tenant/clients')->assertUnauthorized();
    }
    public function test_f04_cliente_final_no_entra_al_panel_del_negocio(): void
    {
        Sanctum::actingAs(User::factory()->create(['role'=>'client','business_id'=>null]));
        $this->getJson('/api/v1/tenant/clients')->assertForbidden();
    }
    public function test_f05_busca_por_telefono_y_no_muestra_fichas_ajenas(): void
    {
        $c=$this->client(); $other=Business::create(['name'=>'Barbería','slug'=>'barberia-test']);
        Client::create(['business_id'=>$other->id,'name'=>'Otra persona','phone'=>'70001234']);
        Sanctum::actingAs($this->admin);
        $this->getJson('/api/v1/tenant/clients?search=70001234')->assertOk()->assertJsonPath('data.total',1)->assertJsonPath('data.data.0.id',$c->id);
    }
    public function test_f06_no_puede_consultar_ni_editar_ficha_ajena(): void
    {
        $c=$this->client(); $other=Business::create(['name'=>'Otro','slug'=>'otro-test']);
        Sanctum::actingAs(User::factory()->create(['role'=>'admin_business','business_id'=>$other->id]));
        $this->getJson('/api/v1/tenant/clients/'.$c->id)->assertNotFound();
        $this->putJson('/api/v1/tenant/clients/'.$c->id,['name'=>'Modificado'])->assertNotFound();
        $this->assertDatabaseHas('clients',['id'=>$c->id,'name'=>'Pedro Pérez']);
    }
    public function test_f07_crea_ficha_y_cita_en_una_operacion(): void
    {
        Sanctum::actingAs($this->admin);
        $r=$this->postJson('/api/v1/tenant/appointments',$this->booking(['new_client'=>['name'=>'Pedro','phone'=>'70001234']]));
        $r->assertCreated()->assertJsonPath('data.client.name','Pedro');
        $this->assertDatabaseCount('clients',1); $this->assertDatabaseCount('appointments',1);
        $this->assertDatabaseHas('appointments',['client_id'=>$r->json('data.client_id'),'business_id'=>$this->business->id]);
    }
    public function test_f08_rechazo_por_horario_no_deja_ficha_huerfana(): void
    {
        Sanctum::actingAs($this->admin);
        $this->postJson('/api/v1/tenant/appointments',$this->booking(['appointment_time'=>'2030-01-07 06:00:00','new_client'=>['name'=>'No guardar']]))->assertStatus(422);
        $this->assertDatabaseCount('clients',0); $this->assertDatabaseCount('appointments',0);
    }
    public function test_f09_reutiliza_cliente_y_conserva_historial(): void
    {
        $c=$this->client(); Sanctum::actingAs($this->admin);
        foreach(['10:00:00','11:00:00'] as $time) $this->postJson('/api/v1/tenant/appointments',$this->booking(['client_id'=>$c->id,'appointment_time'=>'2030-01-07 '.$time]))->assertCreated();
        $this->assertDatabaseCount('clients',1);
        $this->getJson('/api/v1/tenant/clients/'.$c->id.'/appointments')->assertOk()->assertJsonPath('data.total',2);
    }
    public function test_f10_rechaza_doble_cita_del_mismo_cliente_a_la_misma_hora(): void
    {
        $c=$this->client(); Sanctum::actingAs($this->admin);
        $this->postJson('/api/v1/tenant/appointments',$this->booking(['client_id'=>$c->id]))->assertCreated();
        $this->postJson('/api/v1/tenant/appointments',$this->booking(['client_id'=>$c->id,'staff_name'=>'Otro profesional']))->assertStatus(422);
        $this->assertDatabaseCount('appointments',1);
    }
    public function test_f11_reprograma_sin_crear_otra_cita(): void
    {
        $c=$this->client(); Sanctum::actingAs($this->admin);
        $r=$this->postJson('/api/v1/tenant/appointments',$this->booking(['client_id'=>$c->id]));$r->assertCreated();$id=$r->json('data.id');
        $this->putJson('/api/v1/tenant/appointments/'.$id,['appointment_time'=>'2030-01-07 11:00:00'])->assertOk();
        $this->assertDatabaseCount('appointments',1);
        $this->assertDatabaseHas('appointments',['id'=>$id,'client_id'=>$c->id,'appointment_time'=>'2030-01-07 11:00:00']);
    }
    public function test_f12_cancelar_no_borra_cita_ni_historial(): void
    {
        $c=$this->client(); Sanctum::actingAs($this->admin);
        $r=$this->postJson('/api/v1/tenant/appointments',$this->booking(['client_id'=>$c->id]));$r->assertCreated();$id=$r->json('data.id');
        $this->patchJson('/api/v1/tenant/appointments/'.$id.'/status',['status'=>'cancelled'])->assertOk();
        $this->assertDatabaseHas('appointments',['id'=>$id,'status'=>'cancelled']);
        $this->getJson('/api/v1/tenant/clients/'.$c->id.'/appointments')->assertJsonPath('data.total',1);
    }
    public function test_f13_genera_comprobante_y_reporte_del_negocio(): void
    {
        $c=$this->client(); Sanctum::actingAs($this->admin);
        $r=$this->postJson('/api/v1/tenant/appointments',$this->booking(['client_id'=>$c->id]));$r->assertCreated();$id=$r->json('data.id');
        foreach(['/tenant/appointments/'.$id.'/pdf','/tenant/appointments/report/pdf?periodo=semana','/tenant/appointments/report/pdf?periodo=mes'] as $path) {
            $pdf=$this->get('/api/v1'.$path);$pdf->assertOk()->assertHeader('content-type','application/pdf');
            $this->assertStringStartsWith('%PDF',$pdf->getContent());
        }
    }
    public function test_f14_no_publica_endpoints_de_vinculacion(): void
    {
        Sanctum::actingAs($this->admin);
        foreach (['/tenant/clients/1/link-code', '/client-links/preview', '/client-links/confirm'] as $path) {
            $this->postJson('/api/v1'.$path, ['code' => 'unused'])->assertNotFound();
        }
    }
}
