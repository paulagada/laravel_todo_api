<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth/register', [AuthenticationController::class, 'register']);
Route::post('/auth/login', [AuthenticationController::class, 'login']);
Route::post('/auth/send-otp', [AuthenticationController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthenticationController::class, 'verifyOtp']);
// Password reset request route
Route::post('/auth/password/send-otp', [AuthenticationController::class, 'sendPasswordReset']);

// // Password reset form submission route
Route::post('/auth/password/verify-otp', [AuthenticationController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('todos', [TodoController::class, 'getTodos']);
    Route::post('todos', [TodoController::class, 'store']);
    Route::put('todos/{todo}', [TodoController::class, 'update']);
    Route::delete('todos/{todo}', [TodoController::class, 'destroy']);
});