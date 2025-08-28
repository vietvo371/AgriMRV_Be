<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FarmProfileController;
use App\Http\Controllers\Api\MrvDeclarationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PlotController;

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

    // Dashboard
    Route::get('dashboard/summary', [PlotController::class, 'summary']);
    Route::get('dashboard/mrv-overview', [PlotController::class, 'mrvOverview']);
    Route::get('dashboard/land-plots', [PlotController::class, 'landPlots']);
    Route::get('dashboard/land-plots/{plot}', [PlotController::class, 'landPlotDetail']);
    Route::get('dashboard/statistics', [PlotController::class, 'statistics']);
    Route::post('dashboard/land-plots', [PlotController::class, 'createPlot']);
    Route::put('dashboard/land-plots/{plot}', [PlotController::class, 'updatePlot']);
    Route::delete('dashboard/land-plots/{plot}', [PlotController::class, 'deletePlot']);
});

// Token check (validate/refresh)


