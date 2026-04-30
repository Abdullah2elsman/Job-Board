<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;

//public routes

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Job Routes
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Category Routes
Route::get('/categories', [CategoryController::class, 'index']);

// Search Routes
Route::get('/jobs/search', [JobController::class, 'search']);

// protected routes
Route::middleware('auth:sanctum')->group(function ()
{
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user-profile', function (Request $request)
    {
        return $request->user();
    });

    // Job Routes for Employer and Admin
    Route::middleware(['role:employer|admin'])->group(function ()
    {
        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{job}', [JobController::class, 'update']);
        Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
    });

    // Category Routes for Admin
    Route::middleware(['role:admin'])->group(function ()
    {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    // Application Routes
    Route::middleware(['role:candidate'])->group(function ()
    {
        Route::post('/jobs/{id}/apply', [ApplicationController::class, 'apply']);
        Route::get('/my-applications', [ApplicationController::class, 'getMyApplications']);
    });
    Route::middleware(['role:employer|admin'])->group(function ()
    {
        Route::get('/jobs/{jobId}/applications', [ApplicationController::class, 'getJobApplications']);
        Route::put('/applications/{id}/status', [ApplicationController::class, 'updateStatus']);
    });
});
