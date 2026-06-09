<?php

namespace Tests\Feature;

use App\Models\Tontine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TontineTermineeTest extends TestCase
{
    use RefreshDatabase;

    private function donneesValides(): array
    {
        return [
            'nom'                => 'Tontine Test',
            'nombre_membres_max' => 5,
            'montant_cotisation' => 5000,
            'montant_gain'       => 25000,
            'frequence'          => 'mensuel',
            'date_debut'         => now()->subMonth()->toDateString(),
            'date_fin'           => now()->addYear()->toDateString(),
        ];
    }

    public function test_organisateur_peut_acceder_edition_tontine_active(): void
    {
        $organisateur = User::factory()->organisateur()->create();
        $tontine      = Tontine::factory()->create(['organisateur_id' => $organisateur->id]);

        $response = $this->actingAs($organisateur)->get(route('tontines.edit', $tontine));

        $response->assertStatus(200);
    }

    public function test_organisateur_ne_peut_pas_editer_tontine_terminee(): void
    {
        $organisateur = User::factory()->organisateur()->create();
        $tontine      = Tontine::factory()->terminee()->create(['organisateur_id' => $organisateur->id]);

        $response = $this->actingAs($organisateur)->get(route('tontines.edit', $tontine));

        $response->assertStatus(403);
    }

    public function test_organisateur_ne_peut_pas_mettre_a_jour_tontine_terminee(): void
    {
        $organisateur = User::factory()->organisateur()->create();
        $tontine      = Tontine::factory()->terminee()->create(['organisateur_id' => $organisateur->id]);

        $response = $this->actingAs($organisateur)->patch(
            route('tontines.update', $tontine),
            $this->donneesValides()
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('tontines', ['id' => $tontine->id, 'statut' => 'terminee']);
    }

    public function test_organisateur_ne_peut_pas_modifier_tontine_dun_autre_organisateur(): void
    {
        $organisateurA = User::factory()->organisateur()->create();
        $organisateurB = User::factory()->organisateur()->create();
        $tontine       = Tontine::factory()->create(['organisateur_id' => $organisateurB->id]);

        $response = $this->actingAs($organisateurA)->get(route('tontines.edit', $tontine));

        $response->assertStatus(403);
    }
}
