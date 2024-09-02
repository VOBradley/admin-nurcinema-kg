<?php

use Illuminate\Support\Facades\Route;

Route::get('get_dates', [\App\Http\Controllers\TicketonController::class, 'getDates']);
Route::get('get_films', [\App\Http\Controllers\TicketonController::class, 'getFilms']);
Route::get('get_conversation', [\App\Http\Controllers\TicketonController::class, 'getСonversations']);
Route::get('get_film/{id}', [\App\Http\Controllers\TicketonController::class, 'getFilmDetails']);
Route::get('get_premiers', [\App\Http\Controllers\TicketonController::class, 'getPremiers']);
Route::get('get_premier/{filmId}', [\App\Http\Controllers\TicketonController::class, 'getPremierByFilmId']);
Route::post('get_payments', [\App\Http\Controllers\TicketonController::class, 'getPayments']);

Route::prefix('vs')->group(function () {
    Route::get('get_films', [\App\Http\Controllers\TicketonController::class, 'getVsFilms']);
    Route::get('get_dates', [\App\Http\Controllers\TicketonController::class, 'getVsDates']);
    Route::get('get_premiers', [\App\Http\Controllers\TicketonController::class, 'getVsPremiers']);
    Route::get('get_film_detail/{filmId}', [\App\Http\Controllers\TicketonController::class, 'getVsFilmDetails']);
    Route::get('get_about_page', [\App\Http\Controllers\CustomPageController::class, 'getAboutPage']);
    Route::get('get_contact_page', [\App\Http\Controllers\CustomPageController::class, 'getContactPage']);
});

Route::post('user/registration', [\App\Http\Controllers\UserController::class, 'registration']);
Route::post('user/login', [\App\Http\Controllers\UserController::class, 'login']);
Route::post('user/confirm_register', [\App\Http\Controllers\UserController::class, 'confirmRegisterOtp']);
Route::post('user/confirm_create', [\App\Http\Controllers\UserController::class, 'createAccount']);

Route::post('user/restore', [\App\Http\Controllers\UserController::class, 'restore']);
Route::post('user/confirm_restore', [\App\Http\Controllers\UserController::class, 'confirmRestoreOtp']);
Route::post('user/confirm_update_restore', [\App\Http\Controllers\UserController::class, 'updatePasswordByRestore']);
