<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\RestaurantController;
use App\Http\Controllers\API\AuthController;

// --- ROUTES PUBLIQUES ---
Route::get('/restaurants/generate-qr/{id}', [RestaurantController::class, 'generateQrCode']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/menu/{slug}', [RestaurantController::class, 'getBySlug']);
  Route::get('/{id}/qrcode', [RestaurantController::class, 'generateQrCode']);

// Route correcte pour le menu public


// --- ROUTES PROTÉGÉES ---
Route::middleware('auth:sanctum')->group(function () {
    
    // User
    Route::get('/user', function (Request $request) {
        return $request->user()->load('restaurant'); // On charge le resto pour le front
    });

    // Catégories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    // Produits
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

    // Restaurant
    Route::prefix('restaurants')->group(function () {
        Route::get('/{id}', [RestaurantController::class, 'show']);
        Route::put('/{id}', [RestaurantController::class, 'update']);
    });
});