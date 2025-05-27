<?php

namespace Tests\Unit\Infrastructure\Repositories;

use App\Infrastructure\Repositories\FavoriteProductRepository;
use App\Models\FavoriteProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FavoriteProductRepository();
    }

    public function test_get_all_by_user_id_returns_favorites()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $favorite1 = FavoriteProduct::factory()->create(['user_id' => $user->id]);
        $favorite2 = FavoriteProduct::factory()->create(['user_id' => $user->id]);
        $favoriteOther = FavoriteProduct::factory()->create(['user_id' => $otherUser->id]);

        $favorites = $this->repository->getAllByUserId($user->id);

        $this->assertCount(2, $favorites);
        $this->assertTrue($favorites->contains($favorite1));
        $this->assertTrue($favorites->contains($favorite2));
        $this->assertFalse($favorites->contains($favoriteOther));
    }

    public function test_find_by_user_and_product_id_returns_favorite()
    {
        $user = User::factory()->create();
        $favorite = FavoriteProduct::factory()->create([
            'user_id' => $user->id,
            'product_id' => 123,
        ]);

        $found = $this->repository->findByUserAndProductId($user->id, 123);

        $this->assertNotNull($found);
        $this->assertEquals($favorite->id, $found->id);
    }

    public function test_find_by_user_and_product_id_returns_null_if_not_found()
    {
        $user = User::factory()->create();

        $found = $this->repository->findByUserAndProductId($user->id, 999);

        $this->assertNull($found);
    }

    public function test_create_favorite_product()
    {
        $user = User::factory()->create();

        $data = [
            'product_id' => 456,
            'title' => 'Produto Teste',
            'price' => 99.99,
            'image' => 'img.jpg',
            'review' => 4.5,
        ];

        $favorite = $this->repository->create($user->id, $data);

        $this->assertDatabaseHas('favorite_products', [
            'user_id' => $user->id,
            'product_id' => 456,
            'title' => 'Produto Teste',
            'price' => 99.99,
            'image' => 'img.jpg',
            'review' => 4.5,
        ]);

        $this->assertInstanceOf(FavoriteProduct::class, $favorite);
    }

    public function test_delete_favorite_product_returns_true()
    {
        $user = User::factory()->create();
        $favorite = FavoriteProduct::factory()->create([
            'user_id' => $user->id,
            'product_id' => 789,
        ]);

        $result = $this->repository->delete($user->id, 789);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('favorite_products', [
            'user_id' => $user->id,
            'product_id' => 789,
        ]);
    }

    public function test_delete_favorite_product_returns_false_if_not_found()
    {
        $user = User::factory()->create();

        $result = $this->repository->delete($user->id, 999);

        $this->assertFalse($result);
    }
}
