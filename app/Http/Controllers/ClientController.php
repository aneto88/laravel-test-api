<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="Gerenciamento de clientes"
 * )
 */
class ClientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Listar clientes",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $clients = User::all();

        return response()->json($clients);
    }

    /**
     * @OA\Post(
     *     path="/api/clients/register",
     *     summary="Registrar novo cliente",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name","email","password"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="client", type="object"),
     *             @OA\Property(property="access_token", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validatedUser = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create($validatedUser);
        $token = $user->createToken($user->email);

        return response()->json([
            'client' => $user,
            'access_token' => $token->plainTextToken
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/clients/{client}",
     *     summary="Exibir cliente",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do cliente",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    public function show(User $client): JsonResponse
    {
        try {
            $this->validateOwner($client);

            if (!$client) {
                throw new Exception('Product not found.');
            }
            return response()->json([
                $client
            ]);
        }catch (AuthorizationException $e){
            return response()->json(['error' => $e->getMessage()]);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/clients/{client}",
     *     summary="Atualizar cliente",
     *     tags={"Clients"},
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
     *                 required={"name","email"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente atualizado",
     *         @OA\JsonContent(
     *             type="object"
     *         )
     *     )
     * )
     */
    public function update(Request $request, User $client): JsonResponse
    {
        try {
            $this->validateOwner($client);

            $validatedUser = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users'
            ]);

            $client->update($validatedUser);

            return response()->json([
                $client
            ]);
        }catch (AuthorizationException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/clients/{client}",
     *     summary="Deletar cliente",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cliente deletado"
     *     )
     * )
     */
    public function destroy(User $client): JsonResponse
    {
        try {
            $this->validateOwner($client);

            $client->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (AuthorizationException $e){
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    protected function validateOwner($user)
    {
        if (auth()->user()->id !== $user->id) {
            throw new AuthorizationException('Unauthorized');
        }
    }
}
