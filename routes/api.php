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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RegisterStaffController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\SensorHistoryController;
use Illuminate\Support\Facades\Route;

Route::post('/sync/{model}', [SyncController::class, 'receive']);
Route::get('/sync/{model}', [SyncController::class, 'fetch']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/contact-us', [ContactUsController::class, 'store']);
Route::post('/send-test-email', [MailTestController::class, 'send']);
Route::get('/form/sensors_under_alerto', [SensorUnderAlertoController::class, 'index']);
Route::get('/form/sensors_under_ph', [SensorUnderPhController::class, 'index']);
Route::post('/form/staffs', [RegisterStaffController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/form/roles', [RoleController::class, 'index']);
Route::get('/form/regions', [RegionController::class, 'index']);
Route::get('/form/provinces', [ProvinceController::class, 'index']);
Route::get('/form/municipalities', [MunicipalityController::class, 'index']);
Route::get('/form/rivers', [RiverController::class, 'index']);
Route::get('/form/sensor-histories', [SensorHistoryController::class, 'index']);
Route::get('/form/multiselect/regions', [RegionController::class, 'all']);
Route::get('/form/multiselect/provinces', [ProvinceController::class, 'all']);
Route::get('/form/multiselect/municipalities', [MunicipalityController::class, 'all']);

Route::middleware('auth:sanctum')->group(function  () {
   
    Route::resource('/users', UserController::class);
    Route::resource('/roles', RoleController::class);
    // Route::post('/set-password', [AuthController::class, 'setPassword']);
    Route::resource('/sensors_under_alerto', SensorUnderAlertoController::class);
    Route::resource('/sensors_under_ph', SensorUnderPhController::class);
    Route::resource('/regions', RegionController::class);
    Route::get('/multiselect/regions', [RegionController::class, 'all']);
    Route::resource('/provinces', ProvinceController::class);
    Route::get('/multiselect/provinces', [ProvinceController::class, 'all']);
    Route::resource('/rivers', RiverController::class);
    Route::resource('/municipalities', MunicipalityController::class);
    Route::get('/multiselect/municipalities', [MunicipalityController::class, 'all']);
    Route::resource('/thresholds', ThresholdController::class);
    Route::get('/user-logs', [UserLogController::class, 'index']);
    Route::resource('/responses', ResponseController::class);
    //alerts
    Route::get('/alerts-pending', [AlertController::class, 'pending']);
    Route::get('/alerts-responded', [AlertController::class, 'responded']);
    Route::get('/alerts-expired', [AlertController::class, 'expired']);
    Route::delete('/alerts/{id}', [AlertController::class, 'destroy']);
    Route::patch('/alerts/{id}', [AlertController::class, 'update']);

    Route::resource('/staffs', StaffController::class);
    
    // Route::get('/staffs', [StaffController::class, 'index']); // List all
    // Route::post('/staffs/walkin', [StaffController::class, 'storeWalkinStaff']); 
    // Route::patch('/staffs/update/{id}', [StaffController::class, 'update']); // Update
    // Route::delete('/staffs/{id}', [StaffController::class, 'destroy']); // Delete

    Route::patch('/users/{id}/reset-password',[UserController::class, 'resetPassword']);
    Route::patch('/users/{id}/manual-reset-password',[UserController::class, 'manualResetPassword']);
    Route::get('/messages', [ContactMessageController::class, 'index']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        $user = User::with('staff.river')->find($request->user()->id);
        return new UserResource($user);
    });
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/{notification}/seen', [NotificationController::class, 'markAsSeen']);
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });
     Route::get('/fetch-devices', [SensorUnderAlertoController::class, 'fetchDevices']);
     Route::get('/fetch-devices', [SensorUnderPhController::class, 'fetchDevices']);
     Route::get('/sensor-histories', [SensorHistoryController::class, 'index']);
});