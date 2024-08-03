<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SportController;
use App\Http\Controllers\API\ClubController;
use App\Http\Controllers\API\TeamController;
 
  
Route::post('user/register', [UserController::class, 'create']);
Route::post('login', [RegisterController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//Route::middleware('auth:api')->group(function () {
    //Sports EndPoints
    Route::get('/sports', [SportController::class, 'index'])->name('sports');
    Route::post('/sports', [SportController::class, 'create'])->name('store-sports');
    Route::get('/sports/{id}', [SportController::class, 'show'])->name('get-sport');
    Route::put('/sports/{id}', [SportController::class, 'update'])->name('edit-sport');
    Route::delete('/sports/{id}', [SportController::class, 'destroy'])->name('delete-sport');

    //Club EndPoints
    Route::get('/clubs', [ClubController::class, 'index'])->name('clubs');
    Route::post('/clubs', [ClubController::class, 'store'])->name('store-clubs');
    Route::get('/clubs/{id}', [ClubController::class, 'show'])->name('get-club');
    Route::get('/clubs-by-sports/{id}', [ClubController::class, 'bySports'])->name('get-club');
    Route::put('/clubs/{id}', [ClubController::class, 'update'])->name('edit-club');
    Route::delete('/clubs/{id}', [ClubController::class, 'destroy'])->name('delete-club');

    //Team EndPoints
    Route::get('/teams', [TeamController::class, 'index'])->name('teams');
    Route::post('/teams', [TeamController::class, 'store'])->name('store-teams');
    Route::get('/teams/{id}', [TeamController::class, 'show'])->name('get-team');
    Route::put('/teams/{id}', [TeamController::class, 'update'])->name('edit-team');
    Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('delete-team');
//});