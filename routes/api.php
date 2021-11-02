<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Transaction\TransactionController;

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

Route::prefix('accounts')->group(function () {
    Route::post('/create', [AccountController::class, 'create']);
    Route::get('/{account}', [AccountController::class, 'show']);
});

Route::prefix('transactions')->group(function () {
    Route::post('/create', [TransactionController::class, 'create']);
    Route::get('/history/{account}', [TransactionController::class, 'history']);
});
