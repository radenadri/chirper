<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health-check', fn () => response()->noContent())->name('health-check');
Route::get('/static', fn () => response()->json(['status' => true]))->name('static');
Route::get('/http-request', function () {
    $response = Http::get('https://jsonplaceholder.typicode.com/todos/1');

    return $response->json();
})->name('http-request');
