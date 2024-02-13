<?php


use App\Http\Controllers\Vendor\BasicDataController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\CouponController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ShowcaseController;
use App\Http\Controllers\Vendor\SubCategoryController;
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

    /* Showcase Orders */
    Route::get('/showcase', [ShowcaseController::class, 'index']);
    Route::get('/showcase/count', [ShowcaseController::class, 'getShowcaseCount']);

    Route::get('/brand', [BasicDataController::class, 'brand']);
    Route::get('/gender', [BasicDataController::class, 'gender']);
    Route::get('/size', [BasicDataController::class, 'size']);
    Route::get('/color', [BasicDataController::class, 'color']);
    Route::get('/style', [BasicDataController::class, 'style']);
    Route::get('/category', [BasicDataController::class, 'category']);
    Route::get('/sub-category', [BasicDataController::class, 'subcategory']);
    Route::get('/vendor-payment', [BasicDataController::class, 'vendorpayment']);
    Route::resource('/coupon', CouponController::class);

    /*Product Management */
    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'fetchProducts']);
        Route::post('/edit-or-create', [ProductController::class, 'editOrCreateProduct']);
        Route::post('/bulk-upload', [ProductController::class, 'bulkExcelUpload']);
        Route::get('/{id}', [ProductController::class, 'fetchProduct']);
        Route::put('/{id}', [ProductController::class, 'updateProductStatus']);
        Route::get('/{id}/colors', [ProductController::class, 'fetchProductColors']);
        Route::post('/delete-image', [ProductController::class, 'deleteProductImage']);
        Route::post('/upload-images', [ProductController::class, 'uploadProductImages']);

        Route::get('color/{id}', [ProductController::class, 'fetchProductColor']);
        Route::put('color/{id}', [ProductController::class, 'updateProductColorStatus']);
        Route::post('color/{id}/delete-image', [ProductController::class, 'deleteImage']);
        Route::post('color/{id}/upload-images', [ProductController::class, 'uploadImages']);
        Route::post('color/{id}/main-image', [ProductController::class, 'uploadMainImage']);

        Route::get('{product_id}/color/{color_id}/sizes', [ProductController::class, 'fetchSizesByColorId']);
        Route::get('{product_id}/color/{color_id}/sizes/{size_id}', [ProductController::class, 'fetchSizeById']);
        Route::put('{product_id}/color/{color_id}/sizes/{size_id}', [ProductController::class, 'updateSizeById']);
    });
});
