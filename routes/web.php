<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
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

Route::get('/', [MainController::class, 'index']);
Route::get('/event/{event}', [MainController::class, 'event']);
Route::get('/performer/{performer}', [MainController::class, 'performer']);
Route::get('/about', [MainController::class, 'about']);
Route::get('/place/{place}', [MainController::class, 'place']);
Route::match(['get', 'post'], '/search', [MainController::class, 'search']);
Route::post('/reg', [UserController::class, 'reg']);
Route::post('/auth', [UserController::class, 'auth']);
Route::match(['get', 'post'], '/filtIndexEvents', [MainController::class, 'filtIndexEvents']);
Route::match(['get', 'post'], '/filtComments/{event}', [MainController::class, 'filtComments']);
Route::get('/genre/{genre}', [MainController::class, 'genre']);
Route::get('/logout', [UserController::class, 'logout']);
Route::get('/deleteComment/{comment}', [UserController::class, 'deleteComment']);
Route::match(['get', 'post'], '/filtUserSessions/{event}', [MainController::class, 'filtUserSessions']);

Route::group(['middleware' => 'user'], function () {
    Route::get('/account', [MainController::class, 'account']);
    Route::get('/favoritePerformers', [MainController::class, 'favoritePerformers']);
    Route::get('/favoriteEvents', [MainController::class, 'favoriteEvents']);
    Route::get('/favoritePlaces', [MainController::class, 'favoritePlaces']);
    Route::get('/favoriteEvent/{event}', [UserController::class, 'favoriteEvent']);
    Route::get('/favoritePerformer/{performer}', [UserController::class, 'favoritePerformer']);
    Route::get('/favoritePlace/{place}', [UserController::class, 'favoritePlace']);
    Route::get('/watched', [MainController::class, 'watched']);
    Route::get('/comments', [MainController::class, 'comments']);
    Route::get('/tickets', [MainController::class, 'tickets']);
    Route::get('/createTickets', [UserController::class, 'createTickets']);
    Route::get('/ticketReturn', [UserController::class, 'ticketReturn']);
    Route::post('/createComment/{event}', [UserController::class, 'createComment']);
    Route::patch('/editComment', [UserController::class, 'editComment']);
    Route::get('/likeComment/{comment}', [UserController::class, 'likeComment']);
    Route::get('/dislikeComment/{comment}', [UserController::class, 'dislikeComment']);
    Route::patch('/editAcc', [UserController::class, 'editAcc']);
    Route::get('/deleteAccount', [UserController::class, 'deleteAccount']);
    Route::post('/topUpBalance', [UserController::class, 'topUpBalance']);
    Route::post('/subscribe', [UserController::class, 'subscribe']);
    Route::get('/ticketsPDF', [MainController::class, 'ticketsPDF']);
});

Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin', [AdminController::class, 'admin']);

    Route::get('/editEventPage/{event}', [AdminController::class, 'editEventPage']);
    Route::post('/createEvent', [AdminController::class, 'createEvent']);
    Route::patch('/editEvent/{event}', [AdminController::class, 'editEvent']);
    Route::get('/deleteEvent/{event}', [AdminController::class, 'deleteEvent']);
    Route::get('/cancelEvent/{event}', [AdminController::class, 'cancelEvent']);

    Route::get('/adminPerformers', [AdminController::class, 'adminPerformers']);
    Route::get('/editPerformerPage/{performer}', [AdminController::class, 'editPerformerPage']);
    Route::post('/createPerformer', [AdminController::class, 'createPerformer']);
    Route::patch('/editPerformer/{performer}', [AdminController::class, 'editPerformer']);
    Route::get('/deletePerformer/{performer}', [AdminController::class, 'deletePerformer']);

    Route::get('/adminPlaces', [AdminController::class, 'adminPlaces']);
    Route::get('/editPlacePage/{place}', [AdminController::class, 'editPlacePage']);
    Route::post('/createPlace', [AdminController::class, 'createPlace']);
    Route::patch('/editPlace/{place}', [AdminController::class, 'editPlace']);
    Route::get('/deletePlace/{place}', [AdminController::class, 'deletePlace']);

    Route::match(['get', 'post'], '/filtAdminEvents', [AdminController::class, 'filtAdminEvents']);
    Route::match(['get', 'post'], '/filtAdminComments/{event}', [AdminController::class, 'filtAdminComments']);
    Route::match(['get', 'post'], '/filtAdminSessions/{event}', [AdminController::class, 'filtAdminSessions']);
    Route::match(['get', 'post'], '/searchAdmin', [AdminController::class, 'searchAdmin']);
});
