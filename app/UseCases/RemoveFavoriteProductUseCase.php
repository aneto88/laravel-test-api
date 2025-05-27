<?php

namespace App\UseCases;

use App\Domain\Services\FavoriteProductService;
use Exception;

class RemoveFavoriteProductUseCase
{
    private $favoriteProductService;

    public function __construct(FavoriteProductService $favoriteProductService)
    {
        $this->favoriteProductService = $favoriteProductService;
    }

    public function execute(int $userId, int $productId)
    {
        $result = $this->favoriteProductService->removeFavorite($userId, $productId);

        if (!$result) {
            throw new Exception('Favorite product not found.');
        }

        return true;
    }
}
