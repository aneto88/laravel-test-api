<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\FavoriteProductRepositoryInterface;
use App\Models\FavoriteProduct;

class FavoriteProductRepository implements FavoriteProductRepositoryInterface
{
    public function getAllByUserId(int $userId)
    {
        return FavoriteProduct::where('user_id', $userId)->get();
    }

    public function findByUserAndProductId(int $userId, int $productId)
    {
        return FavoriteProduct::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function create(int $userId, array $productData)
    {
        return FavoriteProduct::create([
            'user_id' => $userId,
            'product_id' => $productData['product_id'],
            'title' => $productData['title'],
            'price' => $productData['price'],
            'image' => $productData['image'],
            'review' => $productData['review']
        ]);
    }

    public function delete(int $userId, int $productId): bool
    {
        $favorite = $this->findByUserAndProductId($userId, $productId);

        if (!$favorite) {
            return false;
        }

        return $favorite->delete();
    }
}
