<?php

namespace Tests\Feature\Empleos;

use App\Models\User;
use App\Services\BuscadorEmpleosGetOnBrd;
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

    public function test_el_servicio_de_busqueda_mapea_la_respuesta_de_get_on_board(): void
    {
        Http::fake([
            'www.getonbrd.com/*' => Http::response([
                'data' => [[
                    'id' => 'dev-acme',
                    'type' => 'job',
                    'links' => ['public_url' => 'https://www.getonbrd.com/jobs/dev-acme'],
                    'attributes' => [
                        'title' => 'Backend Developer',
                        'remote' => true,
                        'countries' => ['Remoto'],
                        'min_salary' => 1000,
                        'max_salary' => 2000,
                        'published_at' => now()->timestamp,
                        'company' => ['data' => ['attributes' => ['name' => 'Acme', 'logo' => null]]],
                        'seniority' => ['data' => ['attributes' => ['name' => 'Junior']]],
                    ],
                ]],
                'meta' => ['page' => 1, 'per_page' => 12, 'total_pages' => 1],
            ], 200),
        ]);

        $resultado = app(BuscadorEmpleosGetOnBrd::class)->buscar('programador');

        $this->assertSame(1, $resultado['totalPaginas']);
        $this->assertSame('Acme', $resultado['empleos']->first()['empresa']);
        $this->assertSame('Backend Developer', $resultado['empleos']->first()['titulo']);
        $this->assertSame('Junior', $resultado['empleos']->first()['seniority']);
    }
}
