<?php

namespace App\Models;

use App\Enums\EstadoPostulacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Postulacion extends Model
{
    /** @use HasFactory<\Database\Factories\PostulacionFactory> */
    use HasFactory;

    protected $table = 'postulaciones';

    protected $fillable = [
        'user_id',
        'empresa',
        'cargo',
        'fecha_postulacion',
        'estado',
        'link_vacante',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_postulacion' => 'date',
            'estado' => EstadoPostulacion::class,
            'recordatorio_enviado_en' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
