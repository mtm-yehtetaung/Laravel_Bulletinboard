<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(PostController::class)->group(function () {
    Route::get('/post/list', 'index')->name('postlist');
    Route::get('/post/create', 'showPostCreate')->name('postcreate');
    Route::post('/post/create', 'submitPostCreate')->name('postcreate');
    Route::get('/post/create/confirm', 'showPostConfirm')->name('postconfirm');
    Route::post('/post/create/confirm', 'submitPostConfirm')->name('postconfirm');
    Route::get('/post/edit/{id}', 'showPostEdit')->name('postedit');
    Route::post('/post/edit/{id}', 'submitPostEdit')->name('postedit');
    Route::get('/post/edit/{id}/confirm', 'showPostEditConfirm')->name('posteditconfirm');
    Route::post('/post/edit/{id}/confirm', 'submitPostEditConfirm')->name('posteditconfirm');
    Route::delete('/post/delete/{id}', 'deletePost')->name('postdelete');
    Route::get('post/search','searchPost')->name('postsearch');
});
