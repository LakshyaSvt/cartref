<?php

use App\Http\Controllers\DeliveryHead\DashboardController;
use App\Http\Controllers\DeliveryHead\ShowcaseController;
use App\Http\Controllers\DeliveryBoy\DashboardController as DeliveryBoyDashboardController;
use App\Http\Controllers\DeliveryBoy\ShowcaseController as DeliveryBoyShowcaseController;
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

Route::prefix('delivery-boy')->middleware(['auth', 'delivery-boy'])->group(function () {
    /*Dashboard*/
    Route::get('/dashboard-count', [DeliveryBoyDashboardController::class, 'fetchCount']);

    /* Showcase Orders */
    Route::get('/showcase', [DeliveryBoyShowcaseController::class, 'index']);
    Route::get('/showcase/count', [DeliveryBoyShowcaseController::class, 'getShowcaseCount']);
    Route::get('/delivery-boys', [DeliveryBoyShowcaseController::class, 'getDeliveryBoys']);
    Route::post('/showcase/mark-as-pickup', [DeliveryBoyShowcaseController::class, 'markAsPickup']);
    Route::post('/showcase/mark-as-showcased', [DeliveryBoyShowcaseController::class, 'markAsShowcased']);
    Route::post('/assign-delivery-boys/{order_id}', [DeliveryBoyShowcaseController::class, 'assignDeliveryBoy']);
    Route::post('/showcase/add-time/{id}',[DeliveryBoyShowcaseController::class, 'addTime']);
    Route::post('/showcase/cancel-order/{id}',[DeliveryBoyShowcaseController::class, 'cancelOrder']);
});
