<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return redirect('/admin/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.admin.app');
    })->where('any', '.*');
});

Route::prefix('vendor')->middleware(['auth', 'vendor'])->group(function () {
    Route::get('/', function () {
        return redirect('/vendor/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.vendor.app');
    })->where('any', '.*');
});

Route::prefix('delivery-head')->middleware(['auth', 'delivery-head'])->group(function () {
    Route::get('/', function () {
        return redirect('/delivery-head/dashboard');
    });
    Route::get('{any}', function () {
        return view('panel.delivery-head.app');
    })->where('any', '.*');
});
