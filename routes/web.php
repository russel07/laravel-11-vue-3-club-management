<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DeveloperController;

Route::get('/', function () {
    return view('welcome');
});

//Only For Dev
Route::group(array('prefix' => 'dev'), function () {
    Route::get('/', [DeveloperController::class, 'index'])->name('welcome');
    Route::get('/clear', [DeveloperController::class, 'cacheControl'])->name('cacheControl');
    Route::get('/dbMigrate', [DeveloperController::class, 'dbMigration'])->name('dbMigration');
    Route::get('/dbSeed', [DeveloperController::class, 'dbSeed'])->name('dbSeed');
    Route::get('/composerDump', [DeveloperController::class, 'composerDump'])->name('composerDump');
    Route::get('/passportInstall', 'DeveloperController@passportInstall');
});
