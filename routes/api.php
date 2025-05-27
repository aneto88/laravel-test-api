<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthContoller;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FavoriteProductController;


Route::post('/login', [AuthContoller::class, 'login']);
Route::post('/logout', [AuthContoller::class, 'logout'])->middleware('auth:sanctum');

Route::post('/clients/register', [ClientController::class, 'store']);

Route::prefix('clients')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::get('/{client}', [ClientController::class, 'show']);
    Route::put('/{client}', [ClientController::class, 'update']);
    Route::delete('/{client}', [ClientController::class, 'destroy']);

    Route::middleware(['client.ownership'])->group(function () {
        Route::get('/{client}/favorite-products', [FavoriteProductController::class, 'index']);
        Route::post('/{client}/favorite-products', [FavoriteProductController::class, 'store']);
        Route::delete('/{client}/favorite-products/{productId}', [FavoriteProductController::class, 'destroy']);
    });
});


