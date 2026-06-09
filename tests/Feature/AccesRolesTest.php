<?php

namespace Tests\Feature;

use App\Models\Membre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_membre_ne_peut_pas_creer_une_tontine(): void
    {
        $membre   = Membre::factory()->create();
        $user     = User::factory()->membre()->create(['membre_id' => $membre->id]);

        $response = $this->actingAs($user)->get(route('tontines.create'));

        $response->assertStatus(403);
    }

    public function test_membre_ne_peut_pas_acceder_au_panel_admin(): void
    {
        $membre = Membre::factory()->create();
        $user   = User::factory()->membre()->create(['membre_id' => $membre->id]);

        $response = $this->actingAs($user)->get(route('admin.users'));

        $response->assertStatus(403);
    }

    public function test_organisateur_ne_peut_pas_acceder_au_panel_admin(): void
    {
        $user = User::factory()->organisateur()->create();

        $response = $this->actingAs($user)->get(route('admin.users'));

        $response->assertStatus(403);
    }

    public function test_admin_ne_peut_pas_acceder_aux_tontines(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get(route('tontines.index'));

        $response->assertStatus(403);
    }

    public function test_non_connecte_ne_peut_pas_acceder_aux_tontines(): void
    {
        $response = $this->get(route('tontines.index'));

        $response->assertRedirect(route('login'));
    }
}
