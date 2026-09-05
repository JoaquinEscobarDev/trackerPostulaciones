<?php

namespace App\Livewire\Empleos;

use App\Services\BuscadorEmpleosCareerjet;
use Livewire\Attributes\Url;
use Livewire\Component;

class BuscadorEmpleos extends Component
{
    #[Url(as: 'q', history: true)]
    public string $query = '';

    public int $pagina = 1;

    public bool $buscado = false;

    public function buscar(): void
    {
        $this->pagina = 1;
        $this->buscado = true;
    }

    public function irAPagina(int $pagina): void
    {
        $this->pagina = max(1, $pagina);
    }

    public function render()
    {
        $empleos = collect();
        $totalPaginas = 0;

        if ($this->query !== '' && ($this->buscado || request()->has('q'))) {
            $resultado = app(BuscadorEmpleosCareerjet::class)->buscar(
                $this->query,
                $this->pagina,
                request()->ip(),
                request()->userAgent(),
            );
            $empleos = $resultado['empleos'];
            $totalPaginas = $resultado['totalPaginas'];
        }

        return view('livewire.empleos.buscador-empleos', [
            'empleos' => $empleos,
            'totalPaginas' => $totalPaginas,
        ]);
    }
}
