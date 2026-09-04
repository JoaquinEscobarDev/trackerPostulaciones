<?php

namespace App\Enums;

enum EstadoPostulacion: string
{
    case Postulado = 'Postulado';
    case Entrevista = 'Entrevista';
    case Oferta = 'Oferta';
    case Rechazado = 'Rechazado';

    /**
     * Orden de las columnas del Kanban.
     */
    public static function ordenColumnas(): array
    {
        return [
            self::Postulado,
            self::Entrevista,
            self::Oferta,
            self::Rechazado,
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::Postulado => 'slate',
            self::Entrevista => 'amber',
            self::Oferta => 'emerald',
            self::Rechazado => 'rose',
        };
    }
}
