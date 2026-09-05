<?php

namespace Tests\Feature\Empleos;

use App\Models\User;
use App\Services\BuscadorEmpleosCareerjet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostularDesdeEmpleoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_que_intenta_postular_es_enviado_al_login(): void
    {
        $response = $this->get('/empleos/postular?empresa=Acme&cargo=Dev&link_vacante=https://example.com/job');

        $response->assertRedirect(route('login'));
    }

    public function test_un_usuario_autenticado_es_redirigido_al_kanban_con_los_datos_en_sesion(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->get('/empleos/postular?'.http_build_query([
            'empresa' => 'Acme',
            'cargo' => 'Backend Developer',
            'link_vacante' => 'https://example.com/job',
        ]));

        $response->assertRedirect(route('postulaciones'));
        $response->assertSessionHas('empleo_prefill', [
            'empresa' => 'Acme',
            'cargo' => 'Backend Developer',
            'link_vacante' => 'https://example.com/job',
        ]);
    }

    public function test_requiere_empresa_y_cargo(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->get('/empleos/postular?cargo=Backend%20Developer');

        $response->assertSessionHasErrors('empresa');
    }

    public function test_el_servicio_de_busqueda_mapea_la_respuesta_de_careerjet(): void
    {
        config(['services.careerjet.key' => 'fake-key']);

        Http::fake([
            'search.api.careerjet.net/*' => Http::response([
                'type' => 'JOBS',
                'hits' => 1,
                'pages' => 1,
                'jobs' => [[
                    'title' => 'Backend Developer',
                    'company' => 'Acme',
                    'date' => now()->toRfc2822String(),
                    'description' => 'Excerpt',
                    'locations' => 'Santiago',
                    'salary_min' => 1000,
                    'salary_max' => 2000,
                    'salary_currency_code' => 'CLP',
                    'salary_type' => 'M',
                    'url' => 'https://www.opcionempleo.cl/jobs/dev-acme',
                ]],
            ], 200),
        ]);

        $resultado = app(BuscadorEmpleosCareerjet::class)->buscar('programador');

        $this->assertSame(1, $resultado['totalPaginas']);
        $this->assertSame('Acme', $resultado['empleos']->first()['empresa']);
        $this->assertSame('Backend Developer', $resultado['empleos']->first()['titulo']);
    }

    public function test_sin_api_key_devuelve_una_lista_vacia(): void
    {
        config(['services.careerjet.key' => null]);

        $resultado = app(BuscadorEmpleosCareerjet::class)->buscar('programador');

        $this->assertSame(0, $resultado['totalPaginas']);
        $this->assertTrue($resultado['empleos']->isEmpty());
    }
}
