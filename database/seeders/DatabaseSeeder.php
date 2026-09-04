<?php

namespace Database\Seeders;

use App\Enums\EstadoPostulacion;
use App\Models\Postulacion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        foreach (EstadoPostulacion::cases() as $estado) {
            Postulacion::factory()
                ->count(3)
                ->for($user)
                ->estado($estado)
                ->create();
        }

        // Una postulación "Postulado" sin novedades hace 10 días, para probar el recordatorio.
        Postulacion::factory()
            ->for($user)
            ->estado(EstadoPostulacion::Postulado)
            ->create([
                'empresa' => 'Acme Corp',
                'cargo' => 'Backend Developer (Laravel)',
                'fecha_postulacion' => now()->subDays(10),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ]);
    }
}
