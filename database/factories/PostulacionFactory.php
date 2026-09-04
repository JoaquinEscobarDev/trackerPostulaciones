<?php

namespace Database\Factories;

use App\Enums\EstadoPostulacion;
use App\Models\Postulacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Postulacion>
 */
class PostulacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'empresa' => fake()->company(),
            'cargo' => fake()->jobTitle(),
            'fecha_postulacion' => fake()->dateTimeBetween('-2 months', 'now'),
            'estado' => fake()->randomElement(EstadoPostulacion::cases()),
            'link_vacante' => fake()->boolean(70) ? fake()->url() : null,
            'notas' => fake()->boolean(50) ? fake()->sentence(12) : null,
        ];
    }

    public function estado(EstadoPostulacion $estado): static
    {
        return $this->state(fn () => ['estado' => $estado]);
    }
}
