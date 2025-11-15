<?php

use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\BusinessRoleController;
use App\Http\Controllers\Api\CashbookController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\TransactionController;
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

Route::middleware('auth:sanctum')->group(function () {
    // Businesses
    Route::apiResource('businesses', BusinessController::class);

    // Members
    Route::apiResource('members', MemberController::class);

    // Cashbooks
    Route::apiResource('cashbooks', CashbookController::class);

    // Transactions
    Route::apiResource('transactions', TransactionController::class);

    // Countries, States, Cities
    Route::get('countries', [CountryController::class, 'index']);
    Route::get('states', [StateController::class, 'index']);
    Route::get('cities', [CityController::class, 'index']);

    // Business Roles
    Route::get('business-roles', [BusinessRoleController::class, 'index']);
});
