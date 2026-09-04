<?php

namespace Tests\Feature\Postulaciones;

use App\Enums\EstadoPostulacion;
use App\Livewire\Postulaciones\KanbanBoard;
use App\Livewire\Postulaciones\PostulacionCard;
use App\Livewire\Postulaciones\PostulacionForm;
use App\Models\Postulacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KanbanBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_solo_ve_sus_propias_postulaciones(): void
    {
        $usuario = User::factory()->create();
        $otro = User::factory()->create();

        Postulacion::factory()->for($usuario)->create(['empresa' => 'Mi Empresa']);
        Postulacion::factory()->for($otro)->create(['empresa' => 'Empresa Ajena']);

        Livewire::actingAs($usuario)
            ->test(KanbanBoard::class)
            ->assertSee('Mi Empresa')
            ->assertDontSee('Empresa Ajena');
    }

    public function test_puede_crear_una_postulacion(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(PostulacionForm::class)
            ->call('abrir', null)
            ->set('empresa', 'Anthropic')
            ->set('cargo', 'Backend Engineer')
            ->set('fecha_postulacion', now()->format('Y-m-d'))
            ->set('estado', EstadoPostulacion::Postulado->value)
            ->call('guardar')
            ->assertDispatched('postulacion-guardada');

        $this->assertDatabaseHas('postulaciones', [
            'user_id' => $usuario->id,
            'empresa' => 'Anthropic',
            'cargo' => 'Backend Engineer',
        ]);
    }

    public function test_mover_una_postulacion_actualiza_su_estado(): void
    {
        $usuario = User::factory()->create();
        $postulacion = Postulacion::factory()->for($usuario)->estado(EstadoPostulacion::Postulado)->create();

        Livewire::actingAs($usuario)
            ->test(KanbanBoard::class)
            ->call('moverPostulacion', $postulacion->id, EstadoPostulacion::Entrevista->value);

        $this->assertSame(EstadoPostulacion::Entrevista, $postulacion->fresh()->estado);
    }

    public function test_un_usuario_no_puede_mover_la_postulacion_de_otro(): void
    {
        $usuario = User::factory()->create();
        $otro = User::factory()->create();
        $postulacion = Postulacion::factory()->for($otro)->estado(EstadoPostulacion::Postulado)->create();

        Livewire::actingAs($usuario)
            ->test(KanbanBoard::class)
            ->call('moverPostulacion', $postulacion->id, EstadoPostulacion::Entrevista->value)
            ->assertForbidden();

        $this->assertSame(EstadoPostulacion::Postulado, $postulacion->fresh()->estado);
    }

    public function test_un_usuario_no_puede_editar_la_postulacion_de_otro(): void
    {
        $usuario = User::factory()->create();
        $otro = User::factory()->create();
        $postulacion = Postulacion::factory()->for($otro)->create();

        Livewire::actingAs($usuario)
            ->test(PostulacionForm::class)
            ->call('abrir', $postulacion->id)
            ->assertForbidden();
    }

    public function test_puede_eliminar_su_propia_postulacion(): void
    {
        $usuario = User::factory()->create();
        $postulacion = Postulacion::factory()->for($usuario)->create();

        Livewire::actingAs($usuario)
            ->test(PostulacionCard::class, ['postulacion' => $postulacion])
            ->call('eliminar')
            ->assertDispatched('postulacion-eliminada');

        $this->assertDatabaseMissing('postulaciones', ['id' => $postulacion->id]);
    }

    public function test_un_usuario_no_puede_eliminar_la_postulacion_de_otro(): void
    {
        $usuario = User::factory()->create();
        $otro = User::factory()->create();
        $postulacion = Postulacion::factory()->for($otro)->create();

        Livewire::actingAs($usuario)
            ->test(PostulacionCard::class, ['postulacion' => $postulacion])
            ->call('eliminar')
            ->assertForbidden();

        $this->assertDatabaseHas('postulaciones', ['id' => $postulacion->id]);
    }
}
