<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function  () {
    Route::resource('/users', UserController::class);
    Route::resource('/roles', RoleController::class);
    Route::post('/set-password', [AuthController::class, 'setPassword']);
});