<?php

namespace App\Domain\Services;

use App\Domain\Interfaces\FavoriteProductRepositoryInterface;
use App\Domain\Interfaces\ProductRepositoryInterface;
use Exception;

class FavoriteProductService
{
    private $favoriteProductRepository;
    private $productRepository;

    public function __construct(
        FavoriteProductRepositoryInterface $favoriteProductRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->favoriteProductRepository = $favoriteProductRepository;
        $this->productRepository = $productRepository;
    }

    public function getUserFavorites(int $userId)
    {
        return $this->favoriteProductRepository->getAllByUserId($userId);
    }

    public function addFavorite(int $userId, int $productId)
    {
        $existingFavorite = $this->favoriteProductRepository->findByUserAndProductId($userId, $productId);

        if ($existingFavorite) {
            throw new Exception('This product is already in your favorites.');
        }

        $product = $this->productRepository->getProductById($productId);

        if (!$product) {
            throw new Exception('Product not found.');
        }

        return $this->favoriteProductRepository->create($userId, [
            'product_id' => $productId,
            'title' => $product['title'],
            'price' => $product['price'],
            'image' => $product['image'],
            'review' => $product['rating']['rate'] ?? null
        ]);
    }

    public function removeFavorite(int $userId, int $productId): bool
    {
        return $this->favoriteProductRepository->delete($userId, $productId);
    }
}
