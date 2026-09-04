<?php

namespace App\Livewire\Postulaciones;

use App\Enums\EstadoPostulacion;
use App\Models\Postulacion;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class KanbanBoard extends Component
{
    public function abrirFormularioCreacion(): void
    {
        $this->dispatch('abrir-formulario-postulacion', id: null);
    }

    public function editarPostulacion(int $id): void
    {
        $this->dispatch('abrir-formulario-postulacion', id: $id);
    }

    /**
     * Se llama desde el frontend (SortableJS) al soltar una tarjeta en otra columna.
     */
    public function moverPostulacion(int $id, string $nuevoEstado): void
    {
        $estado = EstadoPostulacion::tryFrom($nuevoEstado);

        if (! $estado) {
            return;
        }

        $postulacion = Postulacion::findOrFail($id);

        $this->authorize('update', $postulacion);

        $postulacion->update(['estado' => $estado]);
    }

    #[On('postulacion-guardada')]
    #[On('postulacion-eliminada')]
    public function refrescar(): void
    {
        // Los listeners no necesitan hacer nada: render() vuelve a
        // consultar la base de datos en cada round-trip de Livewire.
    }

    /**
     * @return Collection<string, Collection<int, Postulacion>>
     */
    protected function columnas(): Collection
    {
        $postulaciones = auth()->user()
            ->postulaciones()
            ->latest('fecha_postulacion')
            ->get();

        return collect(EstadoPostulacion::ordenColumnas())
            ->mapWithKeys(fn (EstadoPostulacion $estado) => [
                $estado->value => $postulaciones->filter(
                    fn (Postulacion $postulacion) => $postulacion->estado === $estado
                ),
            ]);
    }

    public function render()
    {
        return view('livewire.postulaciones.kanban-board', [
            'columnas' => $this->columnas(),
        ]);
    }
}
