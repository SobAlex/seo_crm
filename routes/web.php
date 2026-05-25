<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\WebsiteTypeController;
use App\Http\Controllers\BusinessProcessController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProcessStatusController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// ЭТУ СТРОКУ ПЕРЕМЕСТИЛИ СЮДА (ДО ГРУППЫ auth)
require __DIR__.'/settings.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('clients', ClientController::class);
    Route::resource('website-types', WebsiteTypeController::class);
    Route::resource('business-processes', BusinessProcessController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('process-statuses', ProcessStatusController::class);
    Route::resource('websites', WebsiteController::class);
    Route::resource('keywords', KeywordController::class);
    Route::resource('tracks', TrackController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('tags', TagController::class);
});
