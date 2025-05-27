<?php

namespace App\Domain\Interfaces;
interface ProductRepositoryInterface
{
    public function getProductById(int $productId): ?array;
}
