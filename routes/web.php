<?php

use Illuminate\Support\Facades\Route;
use Laraditz\Lazada\Http\Controllers\SellerController;

Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('/authorized', [SellerController::class, 'authorized'])->name('authorized');
});
