<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Models\Position;
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

Route::get('/users/{id}', [UserController::class, 'index']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [RegisterController::class, 'store']);

Route::get('/token', function () {
    return response()->json(['data' => UserController::getToken()]);
});

Route::get('/positions', function () {
    return response()->json(['data' => Position::all()]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
