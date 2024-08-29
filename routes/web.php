<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::redirect('/', '/home');
Route::resource('post',PostController::class);

Route::get('/home', [PostController::class, 'home'])->name('home');
Route::get('/post/view/{id}', [PostController::class, 'postView']);
Route::get('liked/{id}', [PostController::class, 'likePost']);
Route::get('like-post',[PostController::class, 'LikePostView']);
Route::get('profile/{name}', [PostController::class, 'profile']);
Route::get('deleted/post', [PostController::class, 'softDeleteShow']);
Route::get('restore/{id}', [PostController::class, 'softDeleteRestore']);
Route::get('message/{id}', [PostController::class, 'messageView']);
Route::get('chat', [PostController::class, 'chatView']);

Route::post('force/delete/{id}', [PostController::class, 'forceDelete']);
Route::post('comment/submit', [PostController::class, 'commentSubmit']);
Route::post('comment/delete/{id}',[PostController::class, 'commentDelete']);
Route::post('comment/reply', [PostController::class, 'replyComment']);
Route::post('subcomment/delete/{id}', [PostController::class, 'subcommentDelete']);
Route::post('send/message/', [PostController::class, 'sendMessage']);
Route::post('receive-message', [PostController::class, 'receiveMessage']);
