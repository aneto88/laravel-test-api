<?php

namespace App\Domain\Interfaces;
interface FavoriteProductRepositoryInterface
{
    public function getAllByUserId(int $userId);
    public function findByUserAndProductId(int $userId, int $productId);
    public function create(int $userId, array $productData);
    public function delete(int $userId, int $productId): bool;
}
