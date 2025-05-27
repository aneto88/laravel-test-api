<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Services\FavoriteProductService;
use App\Domain\Interfaces\FavoriteProductRepositoryInterface;
use App\Domain\Interfaces\ProductRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Exception;

class FavoriteProductServiceTest extends TestCase
{
    private $favoriteProductRepository;
    private $productRepository;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->favoriteProductRepository = $this->createMock(FavoriteProductRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $this->service = new FavoriteProductService(
            $this->favoriteProductRepository,
            $this->productRepository
        );
    }

    public function test_get_user_favorites_returns_favorites()
    {
        $userId = 1;
        $favorites = [
            ['product_id' => 10, 'title' => 'Produto 1'],
            ['product_id' => 20, 'title' => 'Produto 2'],
        ];

        $this->favoriteProductRepository
            ->expects($this->once())
            ->method('getAllByUserId')
            ->with($userId)
            ->willReturn($favorites);

        $result = $this->service->getUserFavorites($userId);

        $this->assertEquals($favorites, $result);
    }

    public function test_add_favorite_successfully()
    {
        $userId = 1;
        $productId = 10;
        $product = [
            'title' => 'Produto Teste',
            'price' => 99.99,
            'image' => 'img.jpg',
            'rating' => ['rate' => 4.5]
        ];
        $createdFavorite = [
            'product_id' => $productId,
            'title' => $product['title'],
            'price' => $product['price'],
            'image' => $product['image'],
            'review' => $product['rating']['rate']
        ];

        $this->favoriteProductRepository
            ->expects($this->once())
            ->method('findByUserAndProductId')
            ->with($userId, $productId)
            ->willReturn(null);

        $this->productRepository
            ->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willReturn($product);

        $this->favoriteProductRepository
            ->expects($this->once())
            ->method('create')
            ->with($userId, [
                'product_id' => $productId,
                'title' => $product['title'],
                'price' => $product['price'],
                'image' => $product['image'],
                'review' => $product['rating']['rate']
            ])
            ->willReturn($createdFavorite);

        $result = $this->service->addFavorite($userId, $productId);

        $this->assertEquals($createdFavorite, $result);
    }

    public function test_add_favorite_throws_exception_if_already_favorited()
    {
        $userId = 1;
        $productId = 10;
        $existingFavorite = ['product_id' => $productId];

        $this->favoriteProductRepository
            ->expects($this->once())
            ->method('findByUserAndProductId')
            ->with($userId, $productId)
            ->willReturn($existingFavorite);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This product is already in your favorites.');

        $this->service->addFavorite($userId, $productId);
    }

    public function test_add_favorite_throws_exception_if_product_not_found()
    {
        $userId = 1;
        $productId = 10;

        $this->favoriteProductRepository
            ->expects($this->once())
            ->method('findByUserAndProductId')
            ->with($userId, $productId)
            ->willReturn(null);

        $this->productRepository
            ->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found.');

        $this->service->addFavorite($userId, $productId);
    }

    public function test_remove_favorite_returns_true()
    {
        $userId = 1;
        $productId = 10;

        $this->favoriteProductRepository
            ->expects($this->once())
            ->method('delete')
            ->with($userId, $productId)
            ->willReturn(true);

        $result = $this->service->removeFavorite($userId, $productId);

        $this->assertTrue($result);
    }
}
