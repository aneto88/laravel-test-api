<?php

namespace App\UseCases;

use App\DTOs\FavoriteProductDTO;
use App\Domain\Services\FavoriteProductService;
use Exception;

class GetUserFavoritesUseCase
{
    private FavoriteProductService $favoriteProductService;

    public function __construct(FavoriteProductService $favoriteProductService)
    {
        $this->favoriteProductService = $favoriteProductService;
    }

    public function execute(int $userId)
    {
        $favorites = $this->favoriteProductService->getUserFavorites($userId);
        return FavoriteProductDTO::fromCollection($favorites);
    }
}
