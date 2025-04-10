<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SensorUnderAlertoController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RiverController;
use App\Http\Controllers\SensorUnderPhController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ThresholdController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ContactUsController;
use Illuminate\Support\Facades\Route;

// Route::post('/register', [UserController::class, 'register']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/contact-us', [ContactUsController::class, 'store']);
Route::post('/send-test-email', [MailTestController::class, 'send']);
Route::get('/form/sensors_under_alerto', [SensorUnderAlertoController::class, 'index']);
Route::post('/form/messages', [ContactMessageController::class, 'store']);
Route::post('/form/staffs', [StaffController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/form/roles', [RoleController::class, 'index']);
Route::get('/form/regions', [RegionController::class, 'index']);
Route::get('/form/provinces', [ProvinceController::class, 'index']);
Route::get('/form/municipalities', [MunicipalityController::class, 'index']);
Route::get('/form/rivers', [RiverController::class, 'index']);

Route::middleware('auth:sanctum')->group(function  () {
   
    Route::resource('/users', UserController::class);
    Route::resource('/roles', RoleController::class);
    Route::post('/set-password', [AuthController::class, 'setPassword']);
    Route::resource('/sensors_under_alerto', SensorUnderAlertoController::class);
    Route::resource('/sensors_under_ph', SensorUnderPhController::class);
    Route::resource('/regions', RegionController::class);
    Route::resource('/provinces', ProvinceController::class);
    Route::resource('/rivers', RiverController::class);
    Route::resource('/municipalities', MunicipalityController::class);
    Route::resource('/thresholds', ThresholdController::class);
    Route::get('/user-logs', [UserLogController::class, 'index']);
    Route::resource('/responses', ResponseController::class);
    Route::resource('/alerts', AlertController::class);
    Route::resource('/staffs', StaffController::class);
    Route::patch('/users/{id}/reset-password',[UserController::class, 'resetPassword']);
    Route::get('/messages', [ContactMessageController::class, 'index']);
});