<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_clients()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        User::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/clients');

        $response->assertStatus(200)
            ->assertJsonStructure([['id', 'name', 'email']]);
    }

    public function test_guest_cannot_list_clients()
    {
        $response = $this->getJson('/api/clients');
        $response->assertStatus(401);
    }

    public function test_can_register_new_client()
    {
        $data = [
            'name' => 'Novo Cliente',
            'email' => 'novo@cliente.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/clients/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'client' => ['id', 'name', 'email'],
                'access_token'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'novo@cliente.com'
        ]);
    }

    public function test_authenticated_user_can_view_own_client_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/clients/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $user->id]);
    }

    public function test_user_cannot_view_other_clients_data()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/clients/{$other->id}");

        $response->assertJsonFragment(['error' => 'Unauthorized']);
    }

    public function test_authenticated_user_can_update_own_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $data = [
            'name' => 'Nome Atualizado',
            'email' => 'atualizado@cliente.com'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/clients/{$user->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Nome Atualizado']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'atualizado@cliente.com'
        ]);
    }

    public function test_user_cannot_update_other_clients_data()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $data = [
            'name' => 'Nome Inválido',
            'email' => 'invalido@cliente.com'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/clients/{$other->id}", $data);

        $response->assertJsonFragment(['error' => 'Unauthorized']);
    }

    public function test_authenticated_user_can_delete_own_account()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/clients/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

    public function test_user_cannot_delete_other_clients_account()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/clients/{$other->id}");

        $response->assertJsonFragment(['error' => 'Unauthorized']);
    }
}
