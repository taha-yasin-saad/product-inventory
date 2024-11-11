<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index']);
Route::post('/store-product', [ProductController::class, 'store']);
Route::post('/edit-product/{id}', [ProductController::class, 'edit']);


