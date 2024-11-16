<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;


Route::post("/login", [AuthController::class, 'login'])->name("api.login");
Route::post("/register", [AuthController::class, 'register'])->name("api.register");
