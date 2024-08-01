<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SportController;
 
  
Route::post('register', [UserController::class, 'create']);
Route::post('login', [RegisterController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/sports', [SportController::class, 'index'])->name('sports');
Route::post('/sports', [SportController::class, 'create'])->name('store-sports');
Route::get('/sports/{id}', [SportController::class, 'show'])->name('get-sport');
Route::put('/sports/{id}', [SportController::class, 'update'])->name('edit-sport');
Route::delete('/sports/{id}', [SportController::class, 'destroy'])->name('delete-sport');