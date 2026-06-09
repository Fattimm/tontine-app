<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_avec_bons_identifiants_redirige_vers_dashboard(): void
    {
        $user = User::factory()->organisateur()->create();

        $response = $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_avec_mauvais_mot_de_passe_retourne_erreur(): void
    {
        $user = User::factory()->organisateur()->create();

        $response = $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'mauvais_mot_de_passe',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_acces_dashboard_sans_connexion_redirige_vers_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_logout_deconnecte_et_redirige_vers_login(): void
    {
        $user = User::factory()->organisateur()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
