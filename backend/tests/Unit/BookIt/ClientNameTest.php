<?php
namespace Tests\Unit\BookIt;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use PHPUnit\Framework\TestCase;
class ClientNameTest extends TestCase
{
    private function appointment(?string $notes = null, ?Client $client = null, ?User $user = null): Appointment
    {
        $a = new Appointment(['notes' => $notes]);
        $a->setRelation('client', $client); $a->setRelation('user', $user);
        return $a;
    }
    public function test_u01_prefiere_la_ficha_sobre_el_usuario_que_creo_la_cita(): void
    {
        $a = $this->appointment('Cliente Externo: Nombre anterior', new Client(['name'=>'Pedro']), new User(['name'=>'Administrador']));
        $this->assertSame('Pedro', $a->client_name);
    }
    public function test_u02_recupera_cliente_anterior_sin_ficha(): void
    {
        $a = $this->appointment('Cliente Externo: Ana | Nota de atención', null, new User(['name'=>'Administrador']));
        $this->assertSame('Ana', $a->client_name);
    }
    public function test_u03_elimina_espacios_del_nombre_en_notas(): void
    {
        $this->assertSame('Pedro Pérez', $this->appointment('Cliente Externo:   Pedro Pérez   | Consulta')->client_name);
    }
    public function test_u04_usa_usuario_si_no_hay_ficha_ni_prefijo(): void
    {
        $this->assertSame('Ana', $this->appointment('Reserva personal', null, new User(['name'=>'Ana']))->client_name);
    }
    public function test_u05_presenta_texto_neutro_si_faltan_relaciones(): void
    {
        $this->assertSame('Cliente', $this->appointment()->client_name);
    }
}
