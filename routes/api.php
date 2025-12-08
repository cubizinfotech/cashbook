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
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PaymentMethodController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();

    // Find member record by email or user_id
    $member = \App\Models\Member::with('businessRole')
        ->where('user_id', $user->id)
        ->first();

    return [
        'user' => $user,
        'member' => $member
    ];
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

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Payment Methods
    Route::apiResource('payment-methods', PaymentMethodController::class);

    // Countries, States, Cities, and Business Roles
    Route::get('countries', [CountryController::class, 'index']);
    Route::get('states', [StateController::class, 'index']);
    Route::get('cities', [CityController::class, 'index']);
    Route::get('business-roles', [BusinessRoleController::class, 'index']);
});

