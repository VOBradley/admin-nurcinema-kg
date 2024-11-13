<?php

use App\Http\Controllers\BackgroundListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckAuth;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/', function () {
    $configs = \App\Models\VSConfig::all();
    return Inertia::render('Dashboard', [
        'configs' => $configs
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->middleware(CheckAuth::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'view'])->name('payments');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/remove', [UserController::class, 'remove'])->name('profile.remove');
    Route::post('/change-config', [\App\Http\Controllers\ConfigController::class, 'changeConfig']);

    Route::get('custom_content/{pageSlug}', [\App\Http\Controllers\CustomPageController::class, 'getPage'])->name('custom-content.get');
    Route::post('custom_content/update/{pageSlug}', [\App\Http\Controllers\CustomPageController::class, 'updatePage'])->name('custom-content.update');
    Route::post('custom_content/update-contact', [\App\Http\Controllers\CustomPageController::class, 'updateContact'])->name('custom-content.update-contact');
    Route::get('contacts', [\App\Http\Controllers\CustomPageController::class, 'contactPage'])->name('custom-content.contact');

    Route::prefix('backgrounds')->group(function () {
        Route::get('/', [BackgroundListController::class, 'getBackgrounds'])->name('backgrounds.list');
        Route::post('/create', [BackgroundListController::class, 'createBackground'])->name('backgrounds.create');
        Route::post('/update/{id}', [BackgroundListController::class, 'updateBackground'])->name('backgrounds.update');
        Route::delete('/delete/{id}', [BackgroundListController::class, 'deleteBackground'])->name('backgrounds.delete');
    });
});

require __DIR__.'/auth.php';
