<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('add-product/{id}', [ProductController::class, 'addProductToOrder']);
    Route::patch('order/{id}', [ProductController::class, 'placeOrder']);
    
    Route::resource('users', UserController::class);
});
Route::get('/top-products', [ProductController::class, 'topProducts']);
Route::get('/products', [ProductController::class, 'show']);
Route::get('/orders', [ProductController::class, 'getAllOrders']);






