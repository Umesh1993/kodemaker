<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function(){
//All secure URL's
Route::post("logout", [Usercontroller::class, "logout"]);

Route::post("create-blog", [BlogController::class, "blog"]);
Route::post("update-blog", [BlogController::class, "blog"]);

Route::get('blog-like',[BlogController::class,"blog_like"]);
Route::get('get-single-blog/{id}',[BlogController::class,"get_single_details"]);
Route::get("get-blog-list", [BlogController::class, 'blog_list_api']);
});
Route::get('blog-like',[BlogController::class,"blog_like"]);
Route::get("get-blog-list", [BlogController::class, 'blog_list_api']);


Route::post("user-register", [Usercontroller::class, "user_register"]);
Route::post("login",[UserController::class,'index']);
Route::get("unauthorised", [Usercontroller::class, 'unauthorised']);
