<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculatorController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('calculator')->group(function () {
    Route::post('/calculate', [CalculatorController::class, 'calculate']);
    Route::get('/history', [CalculatorController::class, 'index']);
    Route::delete('/history/{calculation}', [CalculatorController::class, 'destroy']);
    Route::delete('/history', [CalculatorController::class, 'clearAll']);
});