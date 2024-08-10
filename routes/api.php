<?php

use App\Http\Controllers\API\AuthenticateController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\NilaiController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticateController::class)->group(function () {
    Route::post('login', 'login');
});

Route::middleware('auth:api')->group(function () {
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('division', 'divisionData');
        Route::get('employees', 'index');
        Route::get('employees/{uuid}', 'show');
        Route::post('employees', 'store');
        Route::put('employees/{uuid}', 'update');
        Route::delete('employees/{uuid}', 'destroy');
    });

    Route::controller(AuthenticateController::class)->group(function () {
        Route::post('logout', 'logout');
    });
});

// bonus test nilai
Route::controller(NilaiController::class)->group(function () {
    Route::get('nilai/rt', 'index');
    Route::get('nilai/st', 'nilaiST');
});
