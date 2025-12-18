<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LanguageController;

Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
   Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::apiResource('languages', LanguageController::class);
    Route::patch('languages/{language}/toggle', [LanguageController::class, 'toggle'])->name('languages.toggle');

    Route::apiResource('translations', TranslationController::class);
    Route::get('translation/search', [TranslationController::class, 'search']);
});

Route::get('/export/translations', ExportController::class)->name('translations.export');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
