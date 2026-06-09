<?php

namespace Database\Factories;

use App\Models\Membre;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembreFactory extends Factory
{
    protected $model = Membre::class;

    public function definition(): array
    {
        return [
            'nom'       => fake()->lastName(),
            'prenom'    => fake()->firstName(),
            'telephone' => fake()->numerify('7########'),
            'email'     => fake()->unique()->safeEmail(),
            'statut'    => 'actif',
        ];
    }
}
