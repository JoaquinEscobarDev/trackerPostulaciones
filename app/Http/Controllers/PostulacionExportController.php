<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class PostulacionExportController extends Controller
{
    public function csv()
    {
        $postulaciones = auth()->user()->postulaciones()->orderBy('fecha_postulacion')->get();

        $callback = function () use ($postulaciones) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Empresa', 'Cargo', 'Fecha de postulación', 'Estado', 'Link vacante', 'Notas']);

            foreach ($postulaciones as $postulacion) {
                fputcsv($handle, [
                    $postulacion->empresa,
                    $postulacion->cargo,
                    $postulacion->fecha_postulacion->format('Y-m-d'),
                    $postulacion->estado->value,
                    $postulacion->link_vacante,
                    $postulacion->notas,
                ]);
            }

            fclose($handle);
        };

        $nombreArchivo = 'postulaciones-'.now()->format('Y-m-d').'.csv';

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ]);
    }

    public function pdf()
    {
        $postulaciones = auth()->user()->postulaciones()->orderBy('fecha_postulacion')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.postulaciones-pdf', [
            'postulaciones' => $postulaciones,
            'usuario' => auth()->user(),
        ]);

        return $pdf->download('postulaciones-'.now()->format('Y-m-d').'.pdf');
    }
}
