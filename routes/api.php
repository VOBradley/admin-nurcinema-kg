<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('get_dates', [\App\Http\Controllers\TicketonController::class, 'getDates']);
Route::get('get_films', [\App\Http\Controllers\TicketonController::class, 'getFilms']);
Route::get('get_conversation', [\App\Http\Controllers\TicketonController::class, 'getСonversations']);
Route::get('get_film/{id}', [\App\Http\Controllers\TicketonController::class, 'getFilmDetails']);
Route::get('get_premiers', [\App\Http\Controllers\TicketonController::class, 'getPremiers']);
Route::get('get_premier/{filmId}', [\App\Http\Controllers\TicketonController::class, 'getPremierByFilmId']);
Route::post('get_payments', [\App\Http\Controllers\TicketonController::class, 'getPayments']);
