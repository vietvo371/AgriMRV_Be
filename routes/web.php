<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerifierController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/login', function () {
    return view('page.Auth.Login.index');
})->name('login');

Route::post('login', [AuthController::class, 'actionLogin'])->name('login.post');





// Role landing pages (với authentication check)
Route::middleware('web')->group(function () {
    Route::get('/cooperative', function () {
        return view('page.Cooper.index');
    })->name('cooperative');

    Route::get('/dashboard', function () {
        return view('page.Dashboard.index');
    })->name('dashboard');

    // Verifier routes
    Route::get('/verifier', function () {
        return view('page.Verifier.index');
    })->name('verifier.dashboard');

    Route::get('/verifier/schedule', function () {
        return view('page.Verifier.Schedule.index');
    })->name('verifier.schedule');

    Route::get('/verifier/request', function () {
        return view('page.Verifier.Request.index');
    })->name('verifier.request');

    Route::get('/verifier/reports', function () {
        return view('page.Verifier.Reports.index');
    })->name('verifier.reports');

    Route::get('/verifier/analytics', function () {
        return view('page.Verifier.Analytics.index');
    })->name('verifier.analytics');




    Route::get('/bank', function () {
        return view('page.Banker.index');
    })->name('bank');
    Route::get('/government', function () {
        return view('page.Admin.index');
    })->name('government');
    Route::get('/buyer', function () {
        return view('page.Buyer.index');
    })->name('buyer');

    // Logout route
    Route::get('/logout', function () {
        return redirect('/login');
    })->name('logout');
});
