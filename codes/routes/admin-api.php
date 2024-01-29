<?php

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
use App\Http\Controllers\Admin\GenderController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\StyleController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\VendorPaymentController;
use App\Http\Controllers\Admin\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('/user', UserController::class);

    /*Cart and Wishlist */
    Route::get('/wishlist', [WishlistController::class, 'fetchWishlists']);
    Route::get('/cart', [CartController::class, 'fetchCarts']);
    /* Vendor Management */
    Route::resource('/vendor', VendorController::class);
    Route::resource('/vendor-payment', VendorPaymentController::class);
    /*Leads*/
    Route::resource('/contact', ContactController::class);
    Route::resource('/newsletter', NewsLetterController::class);
    /*Collection*/
    Route::resource('/collection-group', CollectionGroupController::class);
    Route::resource('/collection-image', CollectionImageController::class);

    /*Configurations*/
    Route::resource('/home-slider', HomeSliderController::class);
    Route::resource('/component', ComponentController::class);
    Route::resource('/category-component-slider', CategoryComponentSlidersController::class);
    Route::resource('/brand', BrandController::class);
    Route::resource('/gender', GenderController::class);
    Route::resource('/size', SizeController::class);
    Route::resource('/color', ColorController::class);
    Route::resource('/style', StyleController::class);

    /*Control Panel*/
    Route::resource('/seo', SeoController::class);
    Route::resource('/product-review', ProductReviewController::class);
    Route::resource('/coupon', CouponController::class);

    /*Product Management */
    Route::resource('/category', CategoryController::class);
    Route::resource('/sub-category', SubCategoryController::class);

    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'fetchProducts']);
        Route::get('/{id}', [ProductController::class, 'fetchProduct']);
        Route::put('/{id}', [ProductController::class, 'updateProductStatus']);
        Route::get('/{id}/colors', [ProductController::class, 'fetchProductColors']);

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
