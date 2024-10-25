<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [UsersController::class,'login']);
Route::post("/register", [UsersController::class, 'register']);


Route::middleware(['auth:sanctum', 'status.token'])->group(function () {
    Route::prefix("users")->group(function () {
        Route::get("/{user_id}", [UsersController::class, 'getUser']);
        Route::get("/", [UsersController::class, 'getAllUser']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/user/{users_id}', [TasksController::class,'getTasksByUserId']);
        Route::get('/{tasks_id}', [TasksController::class,'getTasksById']);
        // Route::get('/{tasks_id}', [TasksController::class,'getTaskByUserIdByKeyword']);
        Route::post('/', [TasksController::class,'createTasks']);
        Route::put('/{task_id}', [TasksController::class,'updateTasks']);
        Route::delete('/{task_id}', [TasksController::class,'deleteTasks']);
        Route::post('/{tasks_id}/comment', [CommentsController::class,'createComment']);
        Route::get('/{tasks_id}/comment', [CommentsController::class,'getCommentByKeyword']);
        Route::get('/{tasks_id}/getcomment', [CommentsController::class,'getCommentByField']);
    });
    Route::post('/logout', [UsersController::class,'logout']);
});

