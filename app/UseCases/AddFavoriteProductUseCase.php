<?php

namespace App\UseCases;

use App\DTOs\FavoriteProductDTO;
use App\Domain\Services\FavoriteProductService;
use Exception;

class AddFavoriteProductUseCase
{
    private $favoriteProductService;

    public function __construct(FavoriteProductService $favoriteProductService)
    {
        $this->favoriteProductService = $favoriteProductService;
    }

    public function execute(int $userId, int $productId)
    {
        try {
            $favorite = $this->favoriteProductService->addFavorite($userId, $productId);
            return FavoriteProductDTO::fromModel($favorite);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
