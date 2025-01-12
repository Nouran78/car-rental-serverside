<?php

use App\Http\Controllers\CarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::middleware(['auth:sanctum'])->group(function () {

Route::post('/logout', [UserController::class, 'logout']);
Route::get('/user', [UserController::class, 'user']);
Route::put('/profile', [UserController::class, 'updateOwnUser']);


Route::get('/cars', [CarController::class, 'listAllCars']);
Route::get('/cars/{id}', [CarController::class, 'showCar'])->where('id', '[0-9]+'); // Only numbers for ID
Route::get('/cars', [CarController::class, 'searchAndFilterCars']);

Route::post('/orders', [OrderController::class, 'createOrder']);
Route::get('/orders', [OrderController::class, 'listOrders']);



Route::middleware('admin')->group(function () {

Route::post('/cars', [CarController::class, 'addCar']);
Route::delete('/cars/{id}', [CarController::class, 'deleteCar'])->where('id', '[0-9]+');;
Route::post('/orders', [OrderController::class, 'createOrder']);
Route::patch('/orders/{orderId}/payment-status', [OrderController::class, 'updateOrderPaymentStatus']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'getUser']);
Route::put('/users/{user}', [UserController::class, 'updateUser']);

Route::delete('/users/{user}', [UserController::class, 'deleteUser']);

});

});
