<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration
     *
     * @return void
     */
    public function test_register_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test Courier',
            'email' => 'test@test.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    /**
     * Test user login
     *
     * @return void
     */
    public function test_login_user()
    {
        $this->post('/api/register', [
            'name' => 'Test Courier',
            'email' => 'test@test.com',
            'password' => '123456',
        ]);

        $response = $this->post('/api/login', [
            'email' => 'test@test.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    /**
     * Test unauthorized access to a protected route.
     *
     * @return void
     */
    public function test_unauthorized_access()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/couriers/1/vote');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
