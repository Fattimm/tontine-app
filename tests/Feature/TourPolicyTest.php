<?php

namespace Tests\Feature;

use App\Models\Membre;
use App\Models\Tontine;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function creerTour(User $organisateur): Tour
    {
        $tontine = Tontine::factory()->create(['organisateur_id' => $organisateur->id]);
        $membre  = Membre::factory()->create();

        $tontine->membres()->attach($membre->id, [
            'date_adhesion' => now(),
            'statut'        => 'actif',
        ]);

        return Tour::create([
            'tontine_id'  => $tontine->id,
            'membre_id'   => $membre->id,
            'numero_tour' => 1,
            'date_prevue' => today()->addMonth()->toDateString(),
            'statut'      => 'en_attente',
        ]);
    }

    public function test_organisateur_peut_voir_ses_propres_tours(): void
    {
        $organisateur = User::factory()->organisateur()->create();
        $tour         = $this->creerTour($organisateur);

        $response = $this->actingAs($organisateur)->get(route('tours.edit', $tour));

        $response->assertStatus(200);
    }

    public function test_organisateur_ne_peut_pas_modifier_tour_dun_autre_organisateur(): void
    {
        $organisateurA = User::factory()->organisateur()->create();
        $organisateurB = User::factory()->organisateur()->create();
        $tour          = $this->creerTour($organisateurB);

        $response = $this->actingAs($organisateurA)->get(route('tours.edit', $tour));

        $response->assertStatus(403);
    }

    public function test_membre_ne_peut_pas_acceder_edition_tour(): void
    {
        $organisateur = User::factory()->organisateur()->create();
        $tour         = $this->creerTour($organisateur);
        $membre       = Membre::factory()->create();
        $userMembre   = User::factory()->membre()->create(['membre_id' => $membre->id]);

        $response = $this->actingAs($userMembre)->get(route('tours.edit', $tour));

        $response->assertStatus(403);
    }

    public function test_organisateur_ne_peut_pas_supprimer_tour_dun_autre_organisateur(): void
    {
        $organisateurA = User::factory()->organisateur()->create();
        $organisateurB = User::factory()->organisateur()->create();
        $tour          = $this->creerTour($organisateurB);

        $response = $this->actingAs($organisateurA)->delete(route('tours.destroy', $tour));

        $response->assertStatus(403);
        $this->assertDatabaseHas('tours', ['id' => $tour->id]);
    }

    public function test_admin_peut_acceder_edition_tour(): void
    {
        $organisateur = User::factory()->organisateur()->create();
        $tour         = $this->creerTour($organisateur);
        $admin        = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('tours.edit', $tour));

        $response->assertStatus(200);
    }
}
