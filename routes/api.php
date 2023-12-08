<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BuyProductController;
use App\Http\Controllers\BuyProductsController;
use App\Http\Controllers\CardEventsController;
use App\Http\Controllers\CatagoriesController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateLineLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::middleware(['api'])->group(function () {
Route::group(['middleware' => ['auth.user', 'role:admin']], function () {
    Route::get("users", [UserController::class, 'index']);
    Route::post("event", [EventController::class, 'store']);
    Route::post("subevent", [EventController::class, 'storeSub']);
    Route::put("update-event/{id}", [EventController::class, 'update']);
    Route::post("destroy-event/{id}", [EventController::class, 'destroy']);
    Route::post("catagories", [CatagoriesController::class, 'store']);
    Route::post("subcatagories", [CatagoriesController::class, 'storeSub']);
    Route::put("update-catagories/{id}", [CatagoriesController::class, 'update']);
    Route::post("destroy-catagories/{id}", [CatagoriesController::class, 'destroy']);
    Route::post("products", [ProductController::class, 'store']);
    Route::put("update-products/{id}", [ProductController::class, 'update']);
    Route::post("destroy-products/{id}", [ProductController::class, 'destroy']);
    Route::post("cardevent", [CardEventsController::class, 'store']);
    Route::put("update-cardevent/{id}", [CardEventsController::class, 'update']);
    Route::post("destroy-cardevent/{id}", [CardEventsController::class, 'destroy']);
    Route::post("address", [AddressController::class, 'store']);
    // Route::post("order", [OrderController::class, 'store']);

});

Route::group(['middleware' => ['auth.user', 'role:user']], function () {
});

Route::middleware(['auth.user'])->group(function () {
    Route::get("me", [HomeController::class, 'index']); //ดึงข้อมูลฝั่งเรา
    // Route::post("address", [AddressController::class, 'store']);
    Route::post("order", [OrderController::class, 'store']);
    // Route::post("buyproduct", [BuyProductsController::class, 'store']);
}); // ต้อง login ก่อน แต่ role อะไรก็ได้

//อันไหนไม่ต้อง login ไว้ข้างนอก

Route::get('image/{path}', [ImagesController::class, 'show']);
Route::post('image', [ImagesController::class, 'store']);
Route::get("product", [ProductController::class, 'index']);
Route::get("product/{id}", [ProductController::class, 'getId']);
Route::get("cardevent", [CardEventsController::class, 'index']);
Route::get("cardevent/{id}", [CardEventsController::class, 'getId']);
Route::get("parent-event", [EventController::class, 'parentEvent']);
Route::get("parent-catagories", [CatagoriesController::class, 'parentCatagory']);

Route::get("map/address", function (Request $request) {
    $url = "https://api.longdo.com/map/services/address?lon=$request->lon&lat=$request->lat&key=$request->key";
    $respont = Http::get($url);
    return response()->json($respont->json(), $respont->status());
});

Route::post("notify", function (Request $request) {
    $url = "https://notify-api.line.me/api/notify";
    $respont = Http::post($url);
    return response()->json($respont->json(), $respont->status());
});

Route::get("getorder", function () {
    $pp = new \KS\PromptPay();

    $target = '088-656-5433';
    $amount = 420;
    dd($pp->generatePayload($target, $amount));
});
// Route::get("stock", [ProductController::class, 'index']);

//});
