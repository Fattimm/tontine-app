<?php

namespace Database\Factories;

use App\Models\Tontine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TontineFactory extends Factory
{
    protected $model = Tontine::class;

    public function definition(): array
    {
        return [
            'organisateur_id'   => User::factory()->organisateur(),
            'nom'               => fake()->words(3, true),
            'nombre_membres_max'=> 10,
            'description'       => null,
            'montant_cotisation'=> 5000,
            'montant_gain'      => null,
            'frequence'         => 'mensuel',
            'date_debut'        => now()->subMonth()->toDateString(),
            'date_fin'          => null,
            'statut'            => 'active',
        ];
    }

    public function terminee(): static
    {
        return $this->state(['statut' => 'terminee']);
    }

    public function suspendue(): static
    {
        return $this->state(['statut' => 'suspendue']);
    }
}
