<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikesControllerler;
use App\Http\Controllers\PostsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthApiController::class, 'login']);
Route::post('register', [AuthApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [AuthApiController::class, 'logout']);
    Route::get('getUser', [AuthApiController::class, 'get_user']);
    Route::post('save_user_info', [AuthApiController::class, 'saveUserInfo']);
    Route::post('update_user_info', [AuthApiController::class, 'updateUserInfo']);
    //post routes
    Route::post('posts/create', [PostsController::class, 'create']);
    Route::post('posts/update', [PostsController::class, 'update']);
    Route::post('posts/delete', [PostsController::class, 'delete']);
    Route::get('posts', [PostsController::class, 'posts']);
    Route::get('my_posts', [PostsController::class, 'my_posts']);
    //comments routes
    Route::post('comments/create', [CommentsController::class, 'create']);
    Route::post('comments/update', [CommentsController::class, 'update']);
    Route::post('comments/delete', [CommentsController::class, 'delete']);
    Route::get('post/comments', [CommentsController::class, 'comments']);
    //likes routes
    Route::post('post/likes', [LikesControllerler::class, 'likes']);

    Route::post('user/follow', [FollowController::class, 'follow']);

});

