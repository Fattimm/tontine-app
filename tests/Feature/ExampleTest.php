<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_la_page_login_est_accessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }
}
