<?php

use App\Http\Controllers\ChirpController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::get('/health', function () {
    $checks = [
        'database' => false,
    ];

    try {
        DB::connection()->getPdo();
        $checks['database'] = true;
    } catch (Throwable) {
        // database unreachable
    }

    $healthy = ! in_array(false, $checks, strict: true);

    return response()->json([
        'status' => $healthy ? 'ok' : 'degraded',
        'checks' => $checks,
    ], $healthy ? 200 : 503);
})->name('health');

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
