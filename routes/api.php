<?php

use App\Http\Controllers\CardEventsController;
use App\Http\Controllers\CatagoriesController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateLineLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::middleware(['api'])->group(function () {
Route::group(['middleware' => ['auth.user', 'role:admin']], function () {
    Route::get("users", [UserController::class, 'index']);
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
    Route::post("products", [ProductController::class, 'store']);
    Route::put("update-products/{id}", [ProductController::class, 'update']);
    Route::post("destroy-products/{id}", [ProductController::class, 'destroy']);
    Route::get("cardevent", [CardEventsController::class, 'index']);
    Route::post("cardevent", [CardEventsController::class, 'store']);
    Route::put("update-cardevent/{id}", [CardEventsController::class, 'update']);
    Route::post("destroy-cardevent/{id}", [CardEventsController::class, 'destroy']);
});

Route::group(['middleware' => ['auth.user', 'role:user']], function () {
});

Route::middleware(['auth.user'])->group(function () {
    Route::get("me", [HomeController::class, 'index']); //ดึงข้อมูลฝั่งเรา

}); // ต้อง login ก่อน แต่ role อะไรก็ได้

//อันไหนไม่ต้อง login ไว้ข้างนอก

Route::get('image/{path}', [ImagesController::class, 'show']);
Route::post('image', [ImagesController::class, 'store']);
Route::get("product", [ProductController::class, 'index']);
Route::get("product/{id}", [ProductController::class, 'getId']);


// Route::get("stock", [ProductController::class, 'index']);

//});
