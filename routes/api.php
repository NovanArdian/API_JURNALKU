<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SimpleApiController;

// Auth
Route::post('/login', [SimpleApiController::class, 'login']);

// Siswa CRUD
Route::get('/siswas', [SimpleApiController::class, 'index']);
Route::post('/siswas', [SimpleApiController::class, 'store']);
Route::get('/siswas/search', [SimpleApiController::class, 'search']);
Route::get('/siswas/{id}', [SimpleApiController::class, 'show']);
