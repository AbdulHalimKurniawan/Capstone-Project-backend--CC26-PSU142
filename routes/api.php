<?php

use App\Http\Controllers\Api\KlasifikasiKontenController;
use App\Http\Controllers\Api\ScrapeController;
use App\Http\Controllers\Api\SmartMatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/scrape', [ScrapeController::class, 'store']);
Route::get('/klasifikasi-queue', [KlasifikasiKontenController::class, 'getQueue']);
Route::post('/klasifikasi-result', [KlasifikasiKontenController::class, 'updateResult']);
Route::post('/smart-match', [SmartMatchController::class, 'search']);