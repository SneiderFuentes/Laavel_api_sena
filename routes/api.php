<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    //productos
    Route::group([
        'prefix' => 'products'
    ], function ($router) {
        Route::controller(ProductController::class)->group(function () {
            Route::get('', 'index');
            Route::post('',  'store');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}','destroy');
            Route::get('search/search_products', 'search');


        });
    });

    //ventas
    Route::group([
        'prefix' => 'sales'
    ], function ($router) {
        Route::controller(SaleController::class)->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
        });
    });

});
