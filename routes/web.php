<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('web')->get('/sanctum/csrf-cookie', function () {
    return response()->noContent();
});
