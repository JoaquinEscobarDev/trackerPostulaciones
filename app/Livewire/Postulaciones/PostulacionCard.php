<?php

namespace App\Livewire\Postulaciones;

use App\Models\Postulacion;
use Livewire\Component;

class PostulacionCard extends Component
{
    public Postulacion $postulacion;

    public function editar(): void
    {
        $this->dispatch('abrir-formulario-postulacion', id: $this->postulacion->id);
    }

    public function eliminar(): void
    {
        $this->authorize('delete', $this->postulacion);

        $this->postulacion->delete();

        $this->dispatch('postulacion-eliminada');
    }

    public function render()
    {
        return view('livewire.postulaciones.postulacion-card');
    }
}
