<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\CategoryComponentSlidersController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CollectionGroupController;
use App\Http\Controllers\Admin\CollectionImageController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ComponentController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryAreasController;
use App\Http\Controllers\Admin\GenderController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ShowcaseController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\StyleController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\VendorPaymentController;
use App\Http\Controllers\Admin\WishlistController;
use Illuminate\Support\Facades\Route;

Route::post('/product/bulk-upload', [ProductController::class, 'bulkExcelUpload']);

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
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
    Route::delete('/order/{id}', [OrderController::class, 'delete']);

    /* Showcase Orders */
    Route::get('/showcase', [ShowcaseController::class, 'index']);
    Route::get('/showcase/count', [ShowcaseController::class, 'getShowcaseCount']);
    Route::delete('/showcase/{id}', [ShowcaseController::class, 'delete']);
    /* Deliverable Service Area */
    Route::resource('/delivery-area', DeliveryAreasController::class);
    /* Vendor Management */
    Route::resource('/vendor', VendorController::class);
    Route::resource('/vendor-payment', VendorPaymentController::class);
    /* Leads */
    Route::resource('/contact', ContactController::class);
    Route::resource('/newsletter', NewsLetterController::class);
    /* Collection */
    Route::resource('/collection-group', CollectionGroupController::class);
    Route::resource('/collection-image', CollectionImageController::class);
    /* Configurations */
    Route::resource('/home-slider', HomeSliderController::class);
    Route::resource('/component', ComponentController::class);
    Route::resource('/category-component-slider', CategoryComponentSlidersController::class);
    Route::resource('/brand', BrandController::class);
    Route::resource('/gender', GenderController::class);
    Route::resource('/size', SizeController::class);
    Route::resource('/color', ColorController::class);
    Route::resource('/style', StyleController::class);
    /* Control Panel */
    Route::resource('/seo', SeoController::class);
    Route::resource('/product-review', ProductReviewController::class);
    Route::resource('/coupon', CouponController::class);
    /* Cart and Wishlist */
    Route::get('/wishlist', [WishlistController::class, 'fetchWishlists']);
    Route::get('/cart', [CartController::class, 'fetchCarts']);
    /* User Management */
    Route::resource('/user', UserController::class);
    Route::resource('/role', RoleController::class);
    /* Post and Category */
    Route::resource('/post/category', PostCategoryController::class);
    Route::resource('/post', PostController::class);
    /* Announcement */
    Route::resource('/announcement', AnnouncementController::class);

    /*Product Management */
    Route::resource('/category', CategoryController::class);
    Route::resource('/sub-category', SubCategoryController::class);
    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'fetchProducts']);
        Route::post('/edit-or-create', [ProductController::class, 'editOrCreateProduct']);
        //Route::post('/bulk-upload', [ProductController::class, 'bulkExcelUpload']);
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
