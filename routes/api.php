<?php

use App\Http\Controllers\Api\CampaignBriefController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\KlasifikasiKontenController;
use App\Http\Controllers\Api\OptimizationController;
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
Route::post('/smart-matching', [SmartMatchingController::class, 'search']);
Route::post('/optimize-budget', OptimizationController::class);

Route::post('/campaigns', [CampaignController::class, 'store']);
Route::post('/campaigns/{campaign}/select-strategy/{strategy}', [CampaignController::class, 'selectStrategy']);

// Campaign Briefs (Briefing Kampanye)
Route::get('/campaigns/{campaign}/briefs', [CampaignBriefController::class, 'index']);
Route::post('/campaigns/{campaign}/briefs', [CampaignBriefController::class, 'store']);
Route::get('/campaigns/{campaign}/briefs/{brief}', [CampaignBriefController::class, 'show']);
Route::put('/campaigns/{campaign}/briefs/{brief}', [CampaignBriefController::class, 'update']);
Route::delete('/campaigns/{campaign}/briefs/{brief}', [CampaignBriefController::class, 'destroy']);
