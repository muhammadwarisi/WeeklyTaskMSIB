<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix("users")->group(function () {
    Route::get("/{id}", [UsersController::class, 'getUser']);
    Route::post("/create", [UsersController::class, 'createUser']);
});
