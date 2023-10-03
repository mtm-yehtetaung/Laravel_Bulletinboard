<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Post\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::check()){
        return view('postlist');
    }else {
        return view('auth.login');
    }
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ----------------------------auth ------------------------------//
Route::controller(LoginController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});


// ---------------------------- posts ------------------------------//
Route::controller(PostController::class)->group(function () {
    Route::get('/post/list', 'index')->name('postlist');
});