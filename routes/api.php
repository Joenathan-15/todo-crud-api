<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/register", [AuthController::class, "store"]);
Route::post("/login", [AuthController::class, "login"]);


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix("/todo")->group(function () {
        Route::get("/", [TodoController::class, "show"]);
        Route::post("/", [TodoController::class, "create"]);
        Route::patch("/{id}", [TodoController::class, "change_status"]);
        Route::delete("/{id}", [TodoController::class, "destroy_todo"]);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
