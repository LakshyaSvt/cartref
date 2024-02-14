<?php

use App\Http\Controllers\DeliveryHead\DashboardController;
use App\Http\Controllers\DeliveryHead\ShowcaseController;
use Illuminate\Support\Facades\Route;


Route::prefix('delivery-head')->middleware(['auth', 'delivery-head'])->group(function () {
    /*Dashboard*/
    Route::get('/dashboard-count', [DashboardController::class, 'fetchCount']);

    /* Showcase Orders */
    Route::get('/showcase', [ShowcaseController::class, 'index']);
    Route::get('/showcase/count', [ShowcaseController::class, 'getShowcaseCount']);
    Route::get('/delivery-boys', [ShowcaseController::class, 'getDeliveryBoys']);
    Route::post('/assign-delivery-boys/{order_id}', [ShowcaseController::class, 'assignDeliveryBoy']);
});
