<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmProfileController;
use App\Http\Controllers\Api\MrvDeclarationController;
use App\Http\Controllers\Api\VerificationController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('request-otp', [AuthController::class, 'requestOtp']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
Route::middleware('auth:sanctum')->get('check-login', [AuthController::class, 'checkLogin']);


// Protected routes scaffold (controllers will be added next)
Route::middleware('auth:sanctum')->group(function () {
    // Farm & Plot
    Route::apiResource('farm-profiles', FarmProfileController::class);

    // MRV Declaration
    Route::apiResource('mrv-declarations', MrvDeclarationController::class)->only(['index','store','show','update']);

    // Verification (admin or verifier)
    Route::post('verifications', [VerificationController::class, 'store'])->middleware('role:admin,verifier');
});

// Token check (validate/refresh)


