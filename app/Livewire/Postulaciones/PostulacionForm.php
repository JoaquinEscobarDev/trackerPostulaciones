<?php

namespace App\Livewire\Postulaciones;

use App\Enums\EstadoPostulacion;
use App\Models\Postulacion;
use Livewire\Attributes\On;
use Livewire\Component;

class PostulacionForm extends Component
{
    public bool $mostrar = false;

    public ?int $postulacionId = null;

    public string $empresa = '';

    public string $cargo = '';

    public string $fecha_postulacion = '';

    public string $estado = '';

    public ?string $link_vacante = null;

    public ?string $notas = null;

    protected function rules(): array
    {
        return [
            'empresa' => ['required', 'string', 'max:255'],
            'cargo' => ['required', 'string', 'max:255'],
            'fecha_postulacion' => ['required', 'date'],
            'estado' => ['required', 'string', 'in:'.implode(',', array_column(EstadoPostulacion::cases(), 'value'))],
            'link_vacante' => ['nullable', 'url', 'max:2048'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ];
    }

    #[On('abrir-formulario-postulacion')]
    public function abrir(?int $id = null): void
    {
        $this->resetValidation();
        $this->postulacionId = $id;

        if ($id) {
            $postulacion = Postulacion::findOrFail($id);
            $this->authorize('update', $postulacion);

            $this->empresa = $postulacion->empresa;
            $this->cargo = $postulacion->cargo;
            $this->fecha_postulacion = $postulacion->fecha_postulacion->format('Y-m-d');
            $this->estado = $postulacion->estado->value;
            $this->link_vacante = $postulacion->link_vacante;
            $this->notas = $postulacion->notas;
        } else {
            $this->authorize('create', Postulacion::class);

            $this->empresa = '';
            $this->cargo = '';
            $this->fecha_postulacion = now()->format('Y-m-d');
            $this->estado = EstadoPostulacion::Postulado->value;
            $this->link_vacante = null;
            $this->notas = null;
        }

        $this->mostrar = true;
    }

    /**
     * Abre el formulario de creación precargado con datos de una vacante
     * encontrada en el buscador de empleos (ver PostularDesdeEmpleoController).
     */
    #[On('abrir-formulario-postulacion-prefil')]
    public function abrirConDatos(string $empresa, string $cargo, ?string $link_vacante = null): void
    {
        $this->authorize('create', Postulacion::class);
        $this->resetValidation();

        $this->postulacionId = null;
        $this->empresa = $empresa;
        $this->cargo = $cargo;
        $this->fecha_postulacion = now()->format('Y-m-d');
        $this->estado = EstadoPostulacion::Postulado->value;
        $this->link_vacante = $link_vacante;
        $this->notas = null;

        $this->mostrar = true;
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        if ($this->postulacionId) {
            $postulacion = Postulacion::findOrFail($this->postulacionId);
            $this->authorize('update', $postulacion);
            $postulacion->update($datos);
        } else {
            $this->authorize('create', Postulacion::class);
            auth()->user()->postulaciones()->create($datos);
        }

        $this->dispatch('postulacion-guardada');
        $this->cerrar();
    }

    public function eliminar(): void
    {
        if (! $this->postulacionId) {
            return;
        }

        $postulacion = Postulacion::findOrFail($this->postulacionId);
        $this->authorize('delete', $postulacion);
        $postulacion->delete();

        $this->dispatch('postulacion-eliminada');
        $this->cerrar();
    }

    public function cerrar(): void
    {
        $this->reset(['mostrar', 'postulacionId', 'empresa', 'cargo', 'fecha_postulacion', 'estado', 'link_vacante', 'notas']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.postulaciones.postulacion-form', [
            'estados' => EstadoPostulacion::cases(),
        ]);
    }
}
