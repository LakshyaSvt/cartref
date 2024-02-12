<?php


use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\OrderController;
use Illuminate\Support\Facades\Route;

/**
 * @return void
 */


Route::prefix('vendor')->middleware(['auth', 'vendor'])->group(function () {
    /*Dashboard*/
    Route::get('/dashboard-count', [DashboardController::class, 'fetchCount']);

    /* PAN India Orders */
    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/count', [OrderController::class, 'getOrderCount']);
    Route::post('/order/schedule-pickup', [OrderController::class, 'schedulePickup']);
    Route::post('/order/generate-label', [OrderController::class, 'generateLabel']);
    Route::post('/order/cancel-shipment', [OrderController::class, 'cancelShipment']);
    Route::post('/order/mark-as-shipped', [OrderController::class, 'markAsShipped']);
    Route::post('/order/excel-upload', [OrderController::class, 'excelUpload']);
});
