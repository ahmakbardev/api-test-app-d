<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\QuestionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\User\UserTestController;
use App\Http\Controllers\Api\UserAuthController;

// ADMIN
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::middleware('auth:admin')->get('me', [AdminAuthController::class, 'me']);
    Route::middleware('auth:admin')->post('logout', [AdminAuthController::class, 'logout']);
});

// ADMIN CONTENT
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

    // Question General
    Route::get('questions', [QuestionController::class, 'index']);
    Route::post('questions', [QuestionController::class, 'store']);
    Route::get('questions/{id}', [QuestionController::class, 'show']);
    Route::put('questions/{id}', [QuestionController::class, 'update']);
    Route::delete('questions/{id}', [QuestionController::class, 'destroy']);

    // Excel Import
    Route::post('categories/{slug}/questions/import', [QuestionController::class, 'import']);

    // Questions by slug
    Route::get('categories/{slug}/questions', [QuestionController::class, 'indexByCategory']);
    Route::post('categories/{slug}/questions', [QuestionController::class, 'storeByCategory']);
    Route::get('categories/{slug}/questions/{id}', [QuestionController::class, 'showByCategory']);
    Route::put('categories/{slug}/questions/{id}', [QuestionController::class, 'updateByCategory']);
    Route::delete('categories/{slug}/questions/{id}', [QuestionController::class, 'destroyByCategory']);
});
// Excel Template
Route::get('questions/template', [QuestionController::class, 'downloadTemplate']);

Route::prefix('user')->group(function () {
    Route::post('login', [UserAuthController::class, 'login']);
    Route::middleware('auth:user')->get('me', [UserAuthController::class, 'me']);
    Route::middleware('auth:user')->post('logout', [UserAuthController::class, 'logout']);
});

// USER
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// USER CONTENT
Route::middleware('auth:user')->prefix('user')->group(function () {
    Route::post('tests/{slug}/submit', [UserTestController::class, 'submit']);
    Route::get('tests', [UserTestController::class, 'index']);
    Route::get('tests/{id}', [UserTestController::class, 'show']);
});
