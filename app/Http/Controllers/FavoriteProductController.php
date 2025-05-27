<?php

namespace App\Http\Controllers;

use App\UseCases\AddFavoriteProductUseCase;
use App\UseCases\GetUserFavoritesUseCase;
use App\UseCases\RemoveFavoriteProductUseCase;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Favorite Products",
 *     description="Produtos favoritos do cliente"
 * )
 */
class FavoriteProductController extends Controller
{
    private GetUserFavoritesUseCase $getUserFavoritesUseCase;
    private AddFavoriteProductUseCase $addFavoriteProductUseCase;
    private RemoveFavoriteProductUseCase $removeFavoriteProductUseCase;

    public function __construct(
        GetUserFavoritesUseCase $getUserFavoritesUseCase,
        AddFavoriteProductUseCase $addFavoriteProductUseCase,
        RemoveFavoriteProductUseCase $removeFavoriteProductUseCase
    ) {
        $this->getUserFavoritesUseCase = $getUserFavoritesUseCase;
        $this->addFavoriteProductUseCase = $addFavoriteProductUseCase;
        $this->removeFavoriteProductUseCase = $removeFavoriteProductUseCase;
    }

    /**
     * @OA\Get(
     *     path="/api/clients/{client}/favorite-products",
     *     summary="Listar produtos favoritos do cliente",
     *     tags={"Favorite Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos favoritos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $clientId): JsonResponse
    {
        try {
            $favorites = $this->getUserFavoritesUseCase->execute($clientId);
            return response()->json($favorites);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/clients/{client}/favorite-products",
     *     summary="Adicionar produto favorito",
     *     tags={"Favorite Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"product_id"},
     *                 @OA\Property(property="product_id", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto favorito adicionado",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    public function store(Request $request, $clientId): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        $favorite = $this->addFavoriteProductUseCase->execute(
            $clientId,
            $request->input('product_id')
        );

        return response()->json($favorite, Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/clients/{client}/favorite-products/{productId}",
     *     summary="Remover produto favorito",
     *     tags={"Favorite Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Produto favorito removido"
     *     )
     * )
     */
    public function destroy(Request $request, $clientId, $productId): JsonResponse
    {
        try {
            $this->removeFavoriteProductUseCase->execute($clientId, $productId);
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
