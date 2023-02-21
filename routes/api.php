<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AuthController;
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

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/order/create', [OrderController::class, 'create']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns/create', [CampaignController::class, 'create']);
    Route::post('/campaigns/author_category', [CampaignController::class, 'authorAndCategory']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
