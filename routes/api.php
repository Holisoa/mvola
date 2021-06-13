<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use PhpParser\Node\Arg;
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

//route pour essai
Route::get('essai', function () {
    echo 'hello from essay';
});

//public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


//protected routes
Route::group(['middleware' => ['auth:sanctum']],  function () {
    Route::put('depot/{id}', [AuthController::class, 'depot']);
    Route::put('update/{id}', [AuthController::class, 'update']);
    Route::put('transfert/{id}', [AuthController::class, 'transfert']);
    Route::get('show/{id}', [AuthController::class, 'show']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
