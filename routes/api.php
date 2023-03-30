<?php

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

use App\Http\Controllers\API\LogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;

// Route::controller(RegisterController::class)->group(function(){
//     Route::post('register', 'register');
//     Route::post('login', 'login');
// });
        
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('logs', LogController::class);
});