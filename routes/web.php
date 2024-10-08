<?php

use App\Http\Controllers\ChirpController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::get('/test', function () {
    $debugThis = 'debug';

    $debugThis .= ' test123';

    $debugThis .= 'no more';

    return $debugThis;
});

Route::get('chirps', [ChirpController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('chirps');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/exception-test', fn () => throw new Exception('Test exception'));

require __DIR__ . '/auth.php';
