<?php
namespace Tests\Unit\BookIt;
use App\Services\AppointmentValidationService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
class PastAppointmentTest extends TestCase
{
    protected function tearDown(): void { Carbon::setTestNow(); parent::tearDown(); }
    public function test_u06_rechaza_fecha_pasada_antes_de_consultar_la_base(): void
    {
        Carbon::setTestNow(Carbon::parse('2030-01-07 12:00:00', 'America/El_Salvador'));
        $error=(new AppointmentValidationService)->validate(1, '2030-01-07 11:59:00');
        $this->assertIsString($error); $this->assertStringContainsString('transcurrido', $error);
    }
}
