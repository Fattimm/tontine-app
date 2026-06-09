<?php

namespace Tests\Feature;

use App\Models\Membre;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CotisationTest extends TestCase
{
    use RefreshDatabase;

    private function creerScenario(): array
    {
        $organisateur = User::factory()->organisateur()->create();
        $tontine      = Tontine::factory()->create(['organisateur_id' => $organisateur->id]);
        $membre       = Membre::factory()->create();
        $userMembre   = User::factory()->membre()->create(['membre_id' => $membre->id]);

        $tontine->membres()->attach($membre->id, [
            'date_adhesion' => now(),
            'statut'        => 'actif',
        ]);

        return compact('organisateur', 'tontine', 'membre', 'userMembre');
    }

    private function donneesCotisation(array $override = []): array
    {
        return array_merge([
            'montant'       => 5000,
            'date_paiement' => today()->toDateString(),
            'mode_paiement' => 'espece',
            'est_reserve'   => false,
        ], $override);
    }

    public function test_membre_peut_cotiser_pour_sa_tontine_active(): void
    {
        extract($this->creerScenario());

        $response = $this->actingAs($userMembre)->post(route('cotisations.store'),
            $this->donneesCotisation([
                'membre_id'  => $membre->id,
                'tontine_id' => $tontine->id,
            ])
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cotisations', [
            'membre_id'  => $membre->id,
            'tontine_id' => $tontine->id,
            'statut'     => 'paye',
        ]);
    }

    public function test_membre_ne_peut_pas_cotiser_deux_fois_la_meme_periode(): void
    {
        extract($this->creerScenario());

        $data = $this->donneesCotisation([
            'membre_id'  => $membre->id,
            'tontine_id' => $tontine->id,
        ]);

        $this->actingAs($userMembre)->post(route('cotisations.store'), $data);

        $response = $this->actingAs($userMembre)->post(route('cotisations.store'), $data);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('cotisations', 1);
    }

    public function test_cotisation_refusee_pour_tontine_terminee(): void
    {
        extract($this->creerScenario());
        $tontine->update(['statut' => 'terminee']);

        $response = $this->actingAs($userMembre)->post(route('cotisations.store'),
            $this->donneesCotisation([
                'membre_id'  => $membre->id,
                'tontine_id' => $tontine->id,
            ])
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('cotisations', 0);
    }

    public function test_membre_ne_peut_pas_cotiser_pour_tontine_qui_ne_lui_appartient_pas(): void
    {
        $organisateur  = User::factory()->organisateur()->create();
        $tontine       = Tontine::factory()->create(['organisateur_id' => $organisateur->id]);
        $autreMembre   = Membre::factory()->create();
        $userMembre    = User::factory()->membre()->create(['membre_id' => $autreMembre->id]);
        // autreMembre n'est PAS attaché à $tontine

        $response = $this->actingAs($userMembre)->post(route('cotisations.store'),
            $this->donneesCotisation([
                'membre_id'  => $autreMembre->id,
                'tontine_id' => $tontine->id,
            ])
        );

        $response->assertSessionHas('error');
    }

    public function test_organisateur_peut_enregistrer_cotisation_pour_membre(): void
    {
        extract($this->creerScenario());

        $response = $this->actingAs($organisateur)->post(route('cotisations.store'),
            $this->donneesCotisation([
                'membre_id'  => $membre->id,
                'tontine_id' => $tontine->id,
            ])
        );

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cotisations', [
            'membre_id'  => $membre->id,
            'tontine_id' => $tontine->id,
        ]);
    }
}
