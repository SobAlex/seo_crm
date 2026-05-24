<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\WebsiteTypeController;
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
});
