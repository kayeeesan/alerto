<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SensorUnderAlertoController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RiverController;
use App\Http\Controllers\SensorUnderPhController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ThresholdController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function  () {
   
    Route::resource('/users', UserController::class);
    Route::resource('/roles', RoleController::class);
    Route::post('/set-password', [AuthController::class, 'setPassword']);
    Route::resource('/sensors_under_alerto', SensorUnderAlertoController::class);
    Route::resource('/sensors_under_ph', SensorUnderPhController::class);
    Route::resource('/provinces', ProvinceController::class);
    Route::resource('/rivers', RiverController::class);
    Route::resource('/municipalities', MunicipalityController::class);
    Route::resource('/thresholds', ThresholdController::class);
});