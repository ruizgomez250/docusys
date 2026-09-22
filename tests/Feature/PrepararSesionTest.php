<?php

namespace Tests\Feature;

use App\Models\ProyectosEnEstudio;
use App\Models\Sesion;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PrepararSesionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        config(['app.url' => 'http://localhost']);
        app('url')->forceRootUrl('http://localhost');
        $this->withoutMiddleware();
        (require database_path('migrations/2026_07_14_000003_create_sesiones_table.php'))->up();
        (require database_path('migrations/2026_09_22_000001_allow_duplicate_session_numbers.php'))->up();
        Schema::create('mesa_entrada', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('proyectos_en_estudio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mentrada')->nullable();
            $table->integer('nro_expediente');
            $table->unsignedBigInteger('sesion_id')->nullable();
            $table->timestamps();
        });
    }

    private function payload(int $id, string $tipo = 'ORDINARIA', int $numero = 1): array
    {
        return ['doc_ids' => (string) $id, 'tipo_sesion' => $tipo,
            'nro_sesion' => $numero, 'fecha_sesion' => '2026-09-22'];
    }

    public function test_duplicate_session_requires_confirmation_without_assigning_documents(): void
    {
        Sesion::create(['tipo_sesion' => 'ORDINARIA', 'nro_sesion' => 1, 'fecha_sesion' => '2026-09-21']);
        $project = ProyectosEnEstudio::create(['nro_expediente' => 10]);
        $this->postJson(route('proyectos-en-estudio.sesiones.preparar'), $this->payload($project->id, 'ordinaria'))
            ->assertStatus(409)->assertJson(['requiere_confirmacion' => true]);
        $this->assertSame(1, Sesion::count());
        $this->assertNull($project->fresh()->sesion_id);
    }

    public function test_confirmed_duplicate_creates_a_separate_session(): void
    {
        $existing = Sesion::create(['tipo_sesion' => 'ORDINARIA', 'nro_sesion' => 1, 'fecha_sesion' => '2026-09-21']);
        $assigned = ProyectosEnEstudio::create(['nro_expediente' => 9, 'sesion_id' => $existing->id]);
        $project = ProyectosEnEstudio::create(['nro_expediente' => 10]);
        $payload = $this->payload($project->id);
        $payload['confirmar_duplicado'] = true;
        $response = $this->postJson(route('proyectos-en-estudio.sesiones.preparar'), $payload)
            ->assertOk()->assertJson(['success' => true]);
        $this->assertSame(2, Sesion::count());
        $this->assertNotEquals($existing->id, $response->json('sesion_id'));
        $this->assertEquals($response->json('sesion_id'), $project->fresh()->sesion_id);
        $this->assertEquals($existing->id, $assigned->fresh()->sesion_id);
    }

    public function test_available_number_creates_session_and_assigns_only_eligible_documents(): void
    {
        $existing = Sesion::create(['tipo_sesion' => 'ORDINARIA', 'nro_sesion' => 1, 'fecha_sesion' => '2026-09-21']);
        $project = ProyectosEnEstudio::create(['nro_expediente' => 10]);
        $assigned = ProyectosEnEstudio::create(['nro_expediente' => 11, 'sesion_id' => $existing->id]);
        $payload = $this->payload($project->id, 'ORDINARIA', 2);
        $payload['doc_ids'] .= ','.$assigned->id;
        $response = $this->postJson(route('proyectos-en-estudio.sesiones.preparar'), $payload)
            ->assertOk()->assertJson(['success' => true]);
        $this->assertEquals($response->json('sesion_id'), $project->fresh()->sesion_id);
        $this->assertEquals($existing->id, $assigned->fresh()->sesion_id);
    }

    public function test_same_number_is_allowed_for_a_different_session_type(): void
    {
        Sesion::create(['tipo_sesion' => 'ORDINARIA', 'nro_sesion' => 1, 'fecha_sesion' => '2026-09-21']);
        $project = ProyectosEnEstudio::create(['nro_expediente' => 10]);
        $this->postJson(route('proyectos-en-estudio.sesiones.preparar'), $this->payload($project->id, 'EXTRAORDINARIA'))
            ->assertOk()->assertJson(['success' => true]);
        $this->assertSame(2, Sesion::count());
    }
}

