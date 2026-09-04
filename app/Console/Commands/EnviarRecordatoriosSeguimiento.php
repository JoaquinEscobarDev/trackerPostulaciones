<?php

namespace App\Console\Commands;

use App\Enums\EstadoPostulacion;
use App\Jobs\RecordatorioSeguimientoJob;
use App\Models\Postulacion;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('postulaciones:recordatorios')]
#[Description('Encola recordatorios para postulaciones "Postulado" sin seguimiento reciente')]
class EnviarRecordatoriosSeguimiento extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dias = config('postulaciones.dias_recordatorio_seguimiento');
        $limite = now()->subDays($dias);

        $postulaciones = Postulacion::query()
            ->where('estado', EstadoPostulacion::Postulado)
            ->where('updated_at', '<=', $limite)
            ->where(function ($query) {
                $query->whereNull('recordatorio_enviado_en')
                    ->orWhereColumn('recordatorio_enviado_en', '<', 'updated_at');
            })
            ->get();

        foreach ($postulaciones as $postulacion) {
            $diasSinSeguimiento = (int) $postulacion->updated_at->diffInDays(now());

            RecordatorioSeguimientoJob::dispatch($postulacion, $diasSinSeguimiento);
        }

        $this->info("Recordatorios encolados: {$postulaciones->count()}.");

        return self::SUCCESS;
    }
}
