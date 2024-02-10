<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/theme'], function () {
    Route::view('/', 'new-theme.index');
});