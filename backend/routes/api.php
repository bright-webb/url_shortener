<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

Route::post('/login', [APIController::class, 'login']);
Route::post('/shorten', [APIController::class, 'shorten']);
Route::get('/shorten/{code}', [APIController::class, 'code']);
Route::get('/history', [APIController::class, 'history']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

