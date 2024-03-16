<?php

use Illuminate\Support\Facades\Route;

//Route::view('/login', 'panel.login')->middleware('guest');
Route::get('/vendor/login', function () {
    return view('panel.login');
})->middleware('guest')->name('panel.login');

Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/', function () {
        return redirect('/admin/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.admin.app');
    })->where('any', '.*');
});

Route::prefix('vendor')->middleware(['vendor'])->group(function () {
    Route::get('/', function () {
        return redirect('/vendor/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.vendor.app');
    })->where('any', '.*');
});

Route::prefix('delivery-head')->middleware(['delivery-head'])->group(function () {
    Route::get('/', function () {
        return redirect('/delivery-head/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.delivery-head.app');
    })->where('any', '.*');
});

Route::prefix('delivery-boy')->middleware(['delivery-boy'])->group(function () {
    Route::get('/', function () {
        return redirect('/delivery-boy/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.delivery-boy.app');
    })->where('any', '.*');
});
