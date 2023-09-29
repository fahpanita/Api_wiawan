<?php

use App\Http\Controllers\CatagoriesController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateLineLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::middleware(['api'])->group(function () {
Route::get("home", [HomeController::class, 'index'])->middleware(AuthenticateLineLogin::class);
Route::get("user", [UserController::class, 'index']);
// Route::get("stock", [ProductController::class, 'index']);
Route::post("event", [EventController::class, 'store']);
Route::post("subevent", [EventController::class, 'storeSub']);
Route::get("parent-event", [EventController::class, 'parentEvent']);
Route::put("update-event/{id}", [EventController::class, 'update']);
Route::post("destroy-event/{id}", [EventController::class, 'destroy']);
Route::post("catagories", [CatagoriesController::class, 'store']);
Route::post("subcatagories", [CatagoriesController::class, 'storeSub']);
Route::get("parent-catagories", [CatagoriesController::class, 'parentCatagory']);
Route::put("update-catagories/{id}", [CatagoriesController::class, 'update']);
Route::post("destroy-catagories/{id}", [CatagoriesController::class, 'destroy']);
Route::get("product", [ProductController::class, 'index']);
Route::post("products", [ProductController::class, 'store']);
Route::put("update-products/{id}", [ProductController::class, 'update']);
Route::post("destroy-products/{id}", [ProductController::class, 'destroy']);
//});
