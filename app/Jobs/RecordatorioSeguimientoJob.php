<?php

namespace App\Jobs;

use App\Models\Postulacion;
use App\Notifications\PostulacionSinSeguimiento;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordatorioSeguimientoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Postulacion $postulacion,
        public int $diasSinSeguimiento,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // La postulación pudo haber cambiado de estado entre que el
        // comando la encontró y el worker procesó este job.
        if ($this->postulacion->estado !== \App\Enums\EstadoPostulacion::Postulado) {
            return;
        }

        $this->postulacion->user->notify(
            new PostulacionSinSeguimiento($this->postulacion, $this->diasSinSeguimiento)
        );

        // recordatorio_enviado_en no está en $fillable a propósito (es
        // estado interno, no editable desde el formulario), así que se
        // asigna directamente en vez de pasar por update().
        $this->postulacion->recordatorio_enviado_en = now();
        $this->postulacion->save();
    }
}
