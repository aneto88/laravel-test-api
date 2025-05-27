<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Http;

class ExternalProductRepository implements ProductRepositoryInterface
{
    private $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.external_products_api.base_url');    }

    public function getProductById(int $productId): ?array
    {
        $response = Http::get("{$this->apiBaseUrl}/{$productId}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
