<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

//  Register route
Route::post('/register', [AuthController::class, 'register']);

// Login route
Route::post('/login', [AuthController::class, 'login']);

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    //  logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Inventory Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('inventory')->group(function() {
        Route::prefix('admin')->group(function() {
            Route::post('create', [InventoryController::class, 'store']);
            Route::get('list', [InventoryController::class, 'index']);
            Route::put('edit/{id}', [InventoryController::class, 'update']);
            Route::delete('/{id}', [InventoryController::class, 'destroy']);
        });

        Route::prefix('user')->group(function() {
            Route::get('list', [InventoryController::class, 'view_inventories']);
            Route::get('/{id}', [InventoryController::class, 'single_inventory']);

            Route::prefix('cart')->group(function() {
                /// add to cart
                Route::post('add', [InventoryController::class, 'add_to_cart']);
                // view cart
                Route::get('view', [InventoryController::class, 'view_cart']);
                // checkout
                Route::post('checkout', [InventoryController::class, 'checkout']);
            });
        });
    });

});
