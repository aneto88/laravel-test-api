<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use App\UseCases\GetUserFavoritesUseCase;
use App\UseCases\AddFavoriteProductUseCase;
use App\UseCases\RemoveFavoriteProductUseCase;

class FavoriteProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getUserFavoritesUseCase = Mockery::mock(GetUserFavoritesUseCase::class);
        $this->addFavoriteProductUseCase = Mockery::mock(AddFavoriteProductUseCase::class);
        $this->removeFavoriteProductUseCase = Mockery::mock(RemoveFavoriteProductUseCase::class);

        $this->app->instance(GetUserFavoritesUseCase::class, $this->getUserFavoritesUseCase);
        $this->app->instance(AddFavoriteProductUseCase::class, $this->addFavoriteProductUseCase);
        $this->app->instance(RemoveFavoriteProductUseCase::class, $this->removeFavoriteProductUseCase);
    }

    public function test_authenticated_user_can_list_favorite_products()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $favorites = [
            ['id' => 1, 'product_id' => 10, 'name' => 'Produto 1'],
            ['id' => 2, 'product_id' => 20, 'name' => 'Produto 2'],
        ];

        $this->getUserFavoritesUseCase
            ->shouldReceive('execute')
            ->once()
            ->with($user->id)
            ->andReturn($favorites);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/clients/{$user->id}/favorite-products");

        $response->assertStatus(200)
            ->assertJson($favorites);
    }

    public function test_authenticated_user_can_add_favorite_product()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $favorite = ['id' => 1, 'product_id' => 10, 'name' => 'Produto 1'];

        $this->addFavoriteProductUseCase
            ->shouldReceive('execute')
            ->once()
            ->with($user->id, 10)
            ->andReturn($favorite);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/clients/{$user->id}/favorite-products", [
                'product_id' => 10
            ]);

        $response->assertStatus(201)
            ->assertJson($favorite);
    }

    public function test_authenticated_user_can_remove_favorite_product()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->removeFavoriteProductUseCase
            ->shouldReceive('execute')
            ->once()
            ->with($user->id, 10);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/clients/{$user->id}/favorite-products/10");

        $response->assertStatus(204);
    }

    public function test_add_favorite_product_requires_product_id()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/clients/{$user->id}/favorite-products", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['product_id']);
    }
}
