<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    /**
     * @test
     */
    public function can_authenticate()
    {
        $response = $this->postJson('/api/auth/token', [
            'email' => factory(User::class)->create()->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }
}
