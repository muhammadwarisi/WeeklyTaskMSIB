<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix("users")->group(function () {
    Route::get("/login", [UsersController::class, 'loginUser']);
    Route::get("/{id}", [UsersController::class, 'getUser']);
    Route::post("/create", [UsersController::class, 'createUser']);
});

Route::prefix('tasks')->group(function () {
    Route::get('/{id}', [TasksController::class,'getTasks']);
    Route::post('/', [TasksController::class,'createTasks']);
    Route::put('/{id}', [TasksController::class,'updateTasks']);
    Route::delete('/{id}', [TasksController::class,'deleteTasks']);
    Route::post('/{tasks_id}/comment', [CommentsController::class,'createComment']);
    Route::get('/{tasks_id}/comment', [CommentsController::class,'getComment']);
});
